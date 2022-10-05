<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\BankTransaction;
use App\Department;
use App\Expense;
use App\Fund;
use App\FundTransaction;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\MaterialTransfer;
use App\Order;
use App\OrderDetail;
use App\Payment;
use App\ProductTransfer;
use App\Transfer;
use App\UserAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowroomController extends Controller
{
    public function index(){
        $nettingsData = Transfer::with('company')->whereIn('department_id',[3,4])->get()->groupBy('department_id');
        $transfer_products = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query){
            $query->whereIn('department_id', [3,4]);
        })->get()->groupBy('transfer.department_id');
        $departments = Department::get()->keyBy('id');
        return view('admin.showroom.index',compact('nettingsData','departments','transfer_products'));


    }
    public function showroomOrders($id){
        $department_id = $id;
        $orders = Order::with('customer')->where('department_id',$department_id)->get();
        return view('admin.showroom.orders',compact('orders','department_id'));
    }
    public function orderDetails($id){
        $orderDetails = OrderDetail::with('product','color')->where('order_id',$id)->get();
        return view('admin.showroom.orderDetails',compact('orderDetails'));
    }
    public function stock($id){
        $transfer_products = ProductTransfer::with('transfer','product','color')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('product_id');

       $products = MaterialConfig::where('type',2)->get();
       $colors = MaterialConfig::where('type',3)->get()->keyBy('id');

        $transfer_materials = MaterialTransfer::with('transfer','material')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('transfer.department_id');

        $department_id = $id;


        return view('admin.showroom.index',compact('transfer_products','products','colors','department_id'));
    }
    public function orderPayment($id){
        $order = Order::with('customer')->where('id',$id)->first();
        return view('admin.showroom.modal.payment',compact('order'));
    }
    public function orderPaymentStore(Request $request){
        if($request->paid_amount<=0){
            return ['status' => 103, 'message' => 'Paid more then 0'];
        }
        if($request->total_amount<$request->paid_amount){
            return ['status' => 103, 'message' => 'Sorry you can not paid more then due'];
        }

        $order = Order::with('customer')->where('id',$request->order_id)->first();
        $user_account = UserAccount::where('user_id',$order->customer_id)->where('type',2)->first();
        DB::beginTransaction();
        try{
            // Order Due Adjust Start

            $data['paid'] = $request->paid_amount + $order->paid;
            $data['due'] = $request->due_amount;
            $order->update($data);

            // Order Due Adjust End

            // User Account update

            $update_due['total_due'] = $user_account->total_due - $request->paid_amount;
            $user_account->update($update_due);

            // User Account update end

            // Payment data store start
            $payment                  = new Payment();
            $payment->amount          = $request->paid_amount;
            $payment->payment_process = $request->payment_process;
            $payment->payment_info    = $request->payment_info;
            $payment->user_account_id = $user_account->id;
            $payment->releted_id = $request->order_id;
            $payment->releted_id_type = 1;
            $payment->created_by = Auth::user()->id;
            $payment->save();
            // Payment data store end

            if($request->payment_process == 'cash'){
                $fund_info = Fund::where('id',1)->first();
                $bank['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                $fund_info->update($bank);

                $transaction = new FundTransaction();
                $transaction->fund_id = $fund_info->id;
                $transaction->type = 2;
                $transaction->amount = $request->paid_amount;
                $transaction->reason = 'Order Due Payment';
                $transaction->created_by = Auth::user()->id;

                $transaction->save();

            }

            DB::commit();
            return ['status'=>200,'message'=>'Payment Successfully Done'];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function store(Request $request){

        $product_id  = $request->product_id;
        $quantity    = $request->quantity;
        $company_id  = $request->company_id;
        $color_id    = $request->color_id;
        $department_id    = $request->showroom_id;

        $check_quantity = $this->_checkDyeingProductQuantity( $request );
        if ( !$check_quantity ) {
            $material_name = MaterialConfig::find( $request->product_id );
            return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
        }
        DB::beginTransaction();
        try {
        $transfer                      = new Transfer();
        $transferData['company_id']    = $company_id;
        $transferData['department_id'] = $department_id;
        $transferData['created_by']    = Auth::user()->id;
        $transferData['date']          = date( "Y-m-d" );
        $storeTransfer                 = $transfer->create( $transferData );
        $transfer_id = $storeTransfer->id;

        $getAllStock = ProductTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company_id, $product_id ) {
            $query->where( 'company_id', '=', $company_id )->where( 'department_id', '=', 2 )->where( 'product_id', '=', $product_id )->where( 'rest_quantity', '>', 0 );
        } )->get();
        $contentQty = $quantity;
        foreach ( $getAllStock as $stock ) {
            $pro_qty = $contentQty;
            if ( $stock->rest_quantity < $contentQty ) {
                $pro_qty    = $stock->rest_quantity;
                $contentQty = $contentQty - $stock->rest_quantity;

                $productTransfer                     = new ProductTransfer();
                $transferProduct['product_id']       = $product_id;
                $transferProduct['quantity']         = $pro_qty;
                $transferProduct['rest_quantity']    = $pro_qty;
                $transferProduct['transfer_id']      = $transfer_id;
                $transferProduct['product_stock_id'] = $stock->id;
                $transferProduct['color_id']         = $color_id;
//                $transferProduct['process_fee']      = '';
                $transferProduct['created_by']       = Auth::user()->id;
                $storeTransfer                       = $productTransfer->create( $transferProduct );

                if ( $storeTransfer ) {
                    // reduce stock quantity
                    $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                    logger( ' Updated rest qty 1 ' . $data['rest_quantity'] );
                }
            } else {
                $contentQty                          = 0;
                $productTransfer                     = new ProductTransfer();
                $transferProduct['product_id']       = $product_id;
                $transferProduct['quantity']         = $pro_qty;
                $transferProduct['rest_quantity']    = $pro_qty;
                $transferProduct['transfer_id']      = $transfer_id;
                $transferProduct['product_stock_id'] = $stock->id;
                $transferProduct['color_id']         = $color_id;
//                $transferProduct['process_fee']      = '';
                $transferProduct['created_by']       = Auth::user()->id;
                $storeTransfer                       = $productTransfer->create( $transferProduct );
                if ( $storeTransfer ) {
                    // reduce stock quantity
                    $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                    logger( ' Updated rest qty 2 ' . $data['rest_quantity'] );
                }
            }

            if ( $contentQty < 1 ) {
                break;
            }

        }
            DB::commit();

            return ['status' => 200, 'message' => 'Successfully Transfer'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    private function _checkDyeingProductQuantity( $request )
    {
        $company         = $request->company_id;
        $product_id      = $request->product_id;
        $total_stock_qty = ProductTransfer::where( 'product_id', $product_id )->with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company ) {
            $query->where( 'company_id', '=', $company )->where( 'department_id', 2 );
        } )->sum( 'rest_quantity' );
        if ( $total_stock_qty >= $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }

    public function show($id){
//        here id is department id
        $transfer_products = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('transfer.department_id');


    }
}
