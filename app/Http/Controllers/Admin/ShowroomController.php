<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Department;
use App\Expense;
use App\Fund;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\MaterialTransfer;
use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use App\Payment;
use App\Product;
use App\ProductReturn;
use App\ProductTransfer;
use App\Transaction;
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
    public function orderReturn($id){

        $orders = Order::with('details')->where('id',$id)->first();
        $materials = Product::with('color')->where('showroom_id',$orders->department_id)->get()
            ->pluck('color_id','color.name')->prepend( 'Please Select', '' );
        return view('admin.showroom.modal.cart-return',compact('orders','materials'));

    }
    public function orderReturnStore(Request $request){
        DB::beginTransaction();
        try {
            $old_order_info = Order::where('id',$request->order_id)->first();
            $prev_due = $old_order_info->due;
            logger('Prev_due'.$prev_due);
            for ($i = 0; $i < count($request->order_detail_id); $i++) {
                logger('Qty is ' . $request->quantity[$i]);
                if ($request->quantity[$i] != null && $request->quantity[$i] != 0) {
                    $old_order_details = OrderDetail::where('id', $request->order_detail_id[$i])->first();
                    $order_details = OrderDetail::where('id', $request->order_detail_id[$i])->first();
                    logger('order details retrive');
                    if (!$order_details) {
                        return ['status' => 103, 'message' => 'Sorry no order details found'];
                    }
                    if ($order_details->qty < $request->quantity[$i]) {
                        return ['status' => 103, 'message' => 'Sorry you can not return more then you have'];
                    }
//                    if ($order_details->qty != $request->quantity[$i]) {
                        // Order details Product qty reduce start
                        $order_detail_data['qty'] = $order_details->qty - $request->quantity[$i];
                        $order_detail_data['line_total'] = ($order_details->qty - $request->quantity[$i]) * $order_details->selling_price;
                        OrderDetail::where('id', $request->order_detail_id[$i])->update($order_detail_data);
                        logger('Order details updated');
                        // Stock added
                        $product = Product::where('id', $order_details->product_transfer_id)->first();
                        if (!$product) {
                            return ['status' => 103, 'message' => 'Sorry no Product details found'];
                        }
                        $product_data['quantity'] = $product->quantity+$request->quantity[$i];
                        Product::where('id', $order_details->product_transfer_id)->update($product_data);
                        logger('product data updated');

                        $product_transfer = ProductTransfer::where('id', $product->product_transfer_id)->first();
                        logger('product_transfer');
                        if (!$product_transfer) {
                            return ['status' => 103, 'message' => 'Sorry no order details found'];
                        }
                        $product_transfer_data['rest_quantity'] = $product_transfer->rest_quantity + $request->quantity[$i];
                        ProductTransfer::where('id', $product->product_transfer_id)->update($product_transfer_data);
                        logger('$product_transfer_data updated');
//                    }

                    // Order return info added

                    $order_return = new OrderReturn();
                    $order_return->order_details_id = $order_details->id;
                    $order_return->prev_qty = $old_order_details->qty;
                    $order_return->return_qty = $request->quantity[$i];
                    $order_return->qty = ($old_order_details->qty - $request->quantity[$i]);
                    $order_return->created_by = Auth::user()->id;
                    $order_return->save();
                    logger('OrderReturn');
                }
            }
            // Order re-calculate
            $order_details = OrderDetail::where('order_id', $request->order_id)->get();
            $sub_total = $order_details->sum('line_total');
            $order_info = Order::where('id',$request->order_id)->first();
            $order_data['total'] =  $sub_total - $order_info->discount;
            $order_data['sub_total'] =  $sub_total;
            $order_data['due'] =  ($sub_total - $order_info->discount) - $order_info->paid;
            logger('New Due'.$order_data['due']);
            Order::where('id',$request->order_id)->update($order_data);

            $customer_account = UserAccount::where('user_id',$order_info->customer_id)->where('type',2)->first();
            logger("customer_account");
            if (!$customer_account) {
                return ['status' => 103, 'message' => 'Sorry no Customer found'];
            }

            // Order user amount deduct
            $customer_account_data['total_due'] = $customer_account->total_due - ($prev_due - $order_data['due']);
            UserAccount::where('user_id',$order_info->customer_id)->where('type',2)->update($customer_account_data);
            logger('$customer_account_data');

            DB::commit();
            return ['status'=>200,'message'=>'Order Return successfully','department_id'=>$request->department_id];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }

    }
    public function showroomOrders($id){
        $department_id = $id;
        $orders = Order::with('customer')->where('department_id',$department_id)->get();
        return view('admin.showroom.orders',compact('orders','department_id'));
    }
    public function orderPaymentDetail($id){
        $payments = Payment::with('transaction')->where('releted_id',$id)->where('releted_id_type',1)
            ->get();
        return view('admin.showroom.modal.payment-history',compact('payments'));
    }
    public function orderDetails($id){
        $orderDetails = OrderDetail::with('product','color')->where('order_id',$id)->get();
        return view('admin.showroom.orderDetails',compact('orderDetails'));
    }
    public function stock($id){
        $transfer_products = ProductTransfer::with('transfer','color')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('color_id');

       $products = MaterialConfig::where('type',2)->get();
       $colors = MaterialConfig::where('type',3)->get()->keyBy('id');

        $transfer_materials = MaterialTransfer::with('transfer','material')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('transfer.department_id');

        $department_id = $id;


        return view('admin.showroom.index',compact('transfer_products','products','colors','department_id'));
    }
    public function productDetails($department,$color){
       $transfer_products = ProductTransfer::with('transfer','color','expense','material')->where('color_id',$color)->where('process_type',1)
           ->whereHas('transfer', function (Builder $query) use ($department){
            $query->where('department_id', $department);
        })->get();

        return view('admin.showroom.material-details',compact('transfer_products'));

    }
    public function productLossDetails($department,$color){
        $transfer_products = ProductTransfer::with('transfer','color','expense','material')->where('color_id',$color)->where('process_type',2)
            ->whereHas('transfer', function (Builder $query) use ($department){
                $query->where('department_id', $department);
            })->get();

        return view('admin.showroom.material-details',compact('transfer_products'));

    }
    public function finishProductDetails($department,$color){

        $transfer_products = Product::with('color')->where(['showroom_id'=>$department,'color_id'=>$color,'type'=>2])->get();

        return view('admin.showroom.finish-material-details',compact('transfer_products'));

    }
    public function productList($department){
        $colors = MaterialConfig::where('type',3)->get()->keyBy('id');

        $transfer_products = Product::with('color')->where('showroom_id',$department)->get()->groupBy('color_id');

        return view('admin.showroom.product-list',compact('transfer_products','colors','department'));

    }
    public function showroomProductReturn($showroom_id,$color_id){
        $showroomStock = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->where('color_id',$color_id)
            ->where('process_type',1)
            ->whereHas( 'transfer', function ( Builder $query ) use ( $showroom_id ) {
                $query->where( 'department_id', $showroom_id );
            } )->get();
        $type          = 3;
        $showroom_id = $showroom_id;
        $color_id = $color_id;
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->where('color_id',$color_id)
            ->where('process_type',1)
            ->whereHas( 'transfer', function ( Builder $query ) use ( $showroom_id ) {
            $query->where( 'department_id', $showroom_id );
        } )->get()->groupBy( 'color_id' );

        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        return view( 'admin.showroom.modal.return-to-stock', compact('material_key_by','showroomStock', 'color_id','showroom_id', 'type', 'rest_quantity' ) );

    }
    public function getReturnList($department_id,$color_id){
        $return_list = ProductReturn::join('product_transfer','product_return.product_transfer_id','product_transfer.id')
            ->join('material_configs','product_transfer.color_id','material_configs.id')
            ->join('users','product_return.return_by','users.id')
            ->join('transfer','product_transfer.transfer_id','transfer.id')
            ->where('product_return.type',3)
            ->where('transfer.department_id',$department_id)
            ->where('product_transfer.color_id',$color_id)
            ->select('product_return.*','material_configs.name','users.name as return_by_user','transfer.company_id')->get();
        return view( 'admin.showroom.return-list',compact('return_list'));
    }
    public function productReturnStore(Request $request){
        DB::beginTransaction();
        try {
            $showroom_id = $request->showroom_id;
            $color_id = $request->color_id;
            $getAllStock = ProductTransfer::with('transfer')->where('rest_quantity', '>', 0)->where('color_id', $color_id)
                ->where('process_type',1)
                ->whereHas('transfer', function (Builder $query) use ($showroom_id) {
                    $query->where('department_id', $showroom_id);
                })->get();

            $total_quantity = $getAllStock->sum('rest_quantity');
            if ($total_quantity < $request->transfer_stock) {
                return ['status' => 103, 'message' => 'Sorry you can\'t return more then you have'];
            }

            $contentQty = $request->transfer_stock;
            foreach ($getAllStock as $stock) {
                $pro_qty = $contentQty;
                if ($stock->rest_quantity < $contentQty) {
                    $pro_qty = $stock->rest_quantity;
                    $contentQty = $contentQty - $stock->rest_quantity;

                    // Product reduce
                   $product = Product::where('product_transfer_id',$stock->id)->where('color_id',$request->color_id)->first();
                    if(!$product){
                        return ['status' => 103, 'message' => 'Product not found'];
                    }

                   $product_data['quantity'] = $product->quantity - $pro_qty;
                    Product::where('product_transfer_id',$stock->id)->where('color_id',$request->color_id)->update($product_data);

                    // Product Transfer update start
                    $productTransfer['quantity'] = $stock->quantity - $pro_qty;
                    $productTransfer['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    $product_transfer = ProductTransfer::where('id', $stock->id)->first();
                    $product_transfer->update($productTransfer);

                    // Product Transfer update end

                    // Dyeing Product Transfer
                    $dyeing_product_transfer = ProductTransfer::where('id',$stock->product_stock_id)->first();
                    if(!$dyeing_product_transfer){
                        return ['status' => 103, 'message' => 'dyeing_product_transfer not found'];
                    }
                    $dyeingProductTransfer['quantity'] = $dyeing_product_transfer->quantity + $pro_qty;
                    $dyeingProductTransfer['rest_quantity'] = $dyeing_product_transfer->rest_quantity + $pro_qty;
                    ProductTransfer::where('id',$stock->product_stock_id)->update($dyeingProductTransfer);

                    // Product return data store
                    $returnData = new ProductReturn();
                    $returnData->product_transfer_id = $stock->id;
                    $returnData->type = 3;
                    $returnData->quantity = $pro_qty;
                    $returnData->reason = $request->reason;
                    $returnData->return_by = Auth::user()->id;
                    $returnData->save();

                } else {
                    $contentQty = 0;

                    // Product reduce
                    $product = Product::where('product_transfer_id',$stock->id)->where('color_id',$request->color_id)->first();
                    if(!$product){
                        return ['status' => 103, 'message' => 'Product not found'];
                    }
                    $product_data['quantity'] = $product->quantity - $pro_qty;
                    Product::where('product_transfer_id',$stock->id)->where('color_id',$request->color_id)->update($product_data);

                    // Product Transfer update start
                    $productTransfer['quantity'] = $stock->quantity - $pro_qty;
                    $productTransfer['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    $product_transfer = ProductTransfer::where('id', $stock->id)->first();
                    $product_transfer->update($productTransfer);
                    // Product Transfer update end

                    // Dyeing Product Transfer
                    $dyeing_product_transfer = ProductTransfer::where('id',$stock->product_stock_id)->first();
                    if(!$dyeing_product_transfer){
                        return ['status' => 103, 'message' => 'dyeing_product_transfer not found'];
                    }
                    $dyeingProductTransfer['quantity'] = $dyeing_product_transfer->quantity + $pro_qty;
                    $dyeingProductTransfer['rest_quantity'] = $dyeing_product_transfer->rest_quantity + $pro_qty;
                    $product_transfer_dyeing = ProductTransfer::where('id',$stock->product_stock_id)->update($dyeingProductTransfer);

                    // Product return data store
                    $returnData = new ProductReturn();
                    $returnData->product_transfer_id = $stock->id;
                    $returnData->type = 3;
                    $returnData->quantity = $pro_qty;
                    $returnData->reason = $request->reason;
                    $returnData->return_by = Auth::user()->id;
                    $returnData->save();

                }
                if ($contentQty < 1) {
                    break;
                }
            }
            DB::commit();
            return ['status'=>200,'message'=>'Product return to Dyeing'];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }


    }
    public function productCosting($department,$color){
        $colors = MaterialConfig::where('type',3)->get()->keyBy('id');
        $transfer_products = Product::with('color')->where(['showroom_id'=>$department,'color_id'=>$color])->get();
        return view('admin.showroom.product-costing-list',compact('transfer_products','colors'));


    }
    public function productDetailCosting($id){
        $product = Product::with('color')->where('id',$id)->first();
        // 1 is for processed $product
        if($product->type == 1){
            // SHowroom Costing
            $transfer_product_id_for_showroom = ProductTransfer::where('id',$product->product_transfer_id)->first();
            $showroom_product_transfer_id = $transfer_product_id_for_showroom->transfer_id;
            $transfer_product_for_showroom = ProductTransfer::with('transfer')->where('process_type',1)
                ->whereHas('transfer', function (Builder $query) use ($showroom_product_transfer_id){
                    $query->where('id', $showroom_product_transfer_id);
                })->get();

            // Material costing
            $showroom_transfer_product_id = $transfer_product_for_showroom->pluck('id');
            $showroom_transfer_id = $transfer_product_for_showroom->pluck('transfer_id')->unique();
            $material_costing = MaterialTransfer::with('material','detail')->whereIn('product_transfer_id',$showroom_transfer_product_id)
                ->where('transfer_id',$showroom_transfer_id)->get();

            // Dyeing Costing
            $transfer_product_for_showroom_unique = $transfer_product_for_showroom->pluck('product_stock_id')->unique();
            $transfer_product_for_dyeing = ProductTransfer::whereIn('id',$transfer_product_for_showroom_unique)->get();

            // Knitting Costing
            $transfer_product_for_dyeing_unique = $transfer_product_for_dyeing->pluck('product_stock_id')->unique();
            $transfer_product_for_knitting = ProductTransfer::whereIn('id',$transfer_product_for_dyeing_unique)->get();

            // Product Purchase Costing
            $product_for_knitting_unique = $transfer_product_for_knitting->pluck('product_stock_id')->unique();
            $product_in = MaterialIn::with('material')->whereIn('id',$product_for_knitting_unique)->get();

            // Material Config
            $material_config = MaterialConfig::all()->keyBy('id');


        }
        return view('admin.showroom.product-costing-detail',compact('material_config','material_costing','product','transfer_product_for_showroom','transfer_product_for_dyeing','transfer_product_for_knitting','product_in'));


    }
    public function orderPayment($id){
        $order = Order::with('customer')->where('id',$id)->first();
        return view('admin.showroom.modal.payment',compact('order'));
    }
    public function orderPaymentHistory($id){
        $payments = Payment::with('transaction')->where('releted_id',$id)->where('releted_id_type',1)
            ->get();
        return view('admin.showroom.modal.payment-history',compact('payments'));
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
            $payment->payment_process = 'Cash';
            $payment->payment_info    = 'Cash';
            $payment->user_account_id = $user_account->id;
            $payment->releted_id = $request->order_id;
            $payment->releted_department_id = $order->department_id; // 6 is for knitting showroom
            $payment->releted_id_type = 1;  // 1 is order
            $payment->created_by = Auth::user()->id;
            $payment->save();
            // Payment data store end


            // Transaction Store
            $fund_info = Fund::where('department_id',$order->department_id)->first();
            $fund['current_balance'] = $fund_info->current_balance + $request->paid_amount;
            $fund_info->update($fund);

            $transaction = new Transaction();
            $transaction->bank_id = $fund_info->id;
            $transaction->source_type = 2; // 2 is account 1 is bank
            $transaction->type = 2;
            $transaction->date = now();
            $transaction->payment_id = $payment->id;
            $transaction->source_fund_id =  $request->order_id; // Order Id
            $transaction->destination_type =  2; // Fund
            $transaction->amount = $request->paid_amount;
            $transaction->reason = 'Order Payment for Order ID '.$request->order_id;
            $transaction->created_by = Auth::user()->id;
            $transaction->save();

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
