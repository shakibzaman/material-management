<?php

namespace App\Http\Controllers\admin;

use App\Bank;
use App\Fund;
use App\Payment;
use App\Product;
use App\SupplierProduct;
use App\Transaction;
use App\UserAccount;
use DB;
use Gate;
use App\Unit;
use App\Employee;
use App\Supplier;
use App\MaterialIn;
use App\ProductReturn;
use App\MaterialConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if( Gate::denies( 'product_config_access' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $materials = MaterialConfig::where( 'type', 2 )->get();
        return view( 'admin.product.index', compact( 'materials' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if( Gate::denies( 'product_config_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        return view( 'admin.product.create' );
    }

    /**
     * @param $id
     */
    public function returnList( $id )
    {
        // id is Material-in id
        $materialStock = MaterialIn::where( 'id', $id )->first();

        return view( 'admin.productPurchase.modal.return', compact( 'materialStock' ) );
    }

    public function finishPurchaseProductCreate($id){

        abort_if( Gate::denies( 'material_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $showroom_id = $id;
        $materials = MaterialConfig::where( 'type', 3 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $units     = Unit::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $employees = Employee::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $suppliers = Supplier::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        return view( 'admin.product.finish_product_create', compact( 'employees', 'materials', 'units', 'suppliers','showroom_id' ) );
    }

    public function finishPurchaseProductStore(Request $request){


        //  Product Store
        DB::beginTransaction();
        try {
        $user_account_detail = UserAccount::where('user_id',$request->supplier_id)->first();
        if(!$user_account_detail){
            return ['status'=>105,'message'=>'Sorry your Supplier not founded'];
        }

        // Product In history start
        $request['created_by'] = Auth::user()->id;
        $request['rest'] = 0;
        $materialIn = MaterialIn::create($request->all());
        // Product In history end

        if($materialIn){
            // Supplier Bill start
            $request['material_in_id'] = $materialIn->id;
            $request['payment_process'] = $request->payment_process;
            $request['payment_info'] = $request->payment_info;
            $supplier_store = SupplierProduct::create($request->all());

            // Product insert Start

            $product = new Product();
            $product->color_id = $request->material_id;
            $product->product_transfer_id = $materialIn->id;
            $product->showroom_id = $request->showroom_id;
            $product->process_costing = $request->unit_price;
            $product->type = 2; // 2 = Finish Product
            $product->quantity = $request->quantity;
            $product->save();


            if($request->paid_amount>0){
                $request['amount'] = $request->paid_amount;
                $request['user_account_id'] = $user_account_detail->id;
                $request['releted_id'] = $supplier_store->id;
                $request['releted_id_type'] = 2;
                $request['releted_department_id'] = 5;
                $payment = Payment::create($request->all());

                if($request->payment_process == 'bank'){
                    $bank_info = Bank::where('id',$request->payment_type)->first();
                    $bank['current_balance'] = $bank_info->current_balance - $request->paid_amount;
                    $bank_info->update($bank);

                    $transaction = new Transaction();
                    $transaction->bank_id = $bank_info->id;
                    $transaction->date = now();
                    $transaction->source_type = 1; // 2 is account 1 is bank
                    $transaction->type = 1; // 1 is Widthrow
                    $transaction->payment_id = $payment->id;
                    $transaction->amount = $request->paid_amount;
                    $transaction->reason = 'Supplier Payment for finish Product';
                    $transaction->created_by = Auth::user()->id;

                    $transaction->save();

                }
                if($request->payment_process == 'account'){
                    $fund_info = Fund::where('id',$request->payment_type)->first();
                    $fund['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                    $fund_info->update($fund);

                    $transaction = new Transaction();
                    $transaction->bank_id = $fund_info->id;
                    $transaction->source_type = 2; // 2 is account 1 is bank
                    $transaction->type = 1;
                    $transaction->date = now();
                    $transaction->payment_id = $payment->id;
                    $transaction->amount = $request->paid_amount;
                    $transaction->reason = 'Supplier Payment for finish Product';
                    $transaction->created_by = Auth::user()->id;

                    $transaction->save();

                }
            }


            $user_account['total_due'] = $user_account_detail->total_due + ($request->total_price - $request->paid_amount);
            $user_account['total_paid'] = $user_account_detail->total_paid + $request->paid_amount;
            $user_account_detail->update($user_account);

        }

        DB::commit();
            if($request->showroom_id == 3)
            {
                return redirect()->route('admin.showroom.stock',3);
            }
            if($request->showroom_id == 4)
            {
                return redirect()->route('admin.showroom.stock',4);
            }
        } catch (\Exception $e) {

        DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function returnStock( Request $request )
    {
        $material_in_details = MaterialIn::where( 'id', $request->id )->first();
        $product_id          = $material_in_details->material_id;

        $check_quantity = $this->_checkProductQuantity( $request );
        if ( !$check_quantity ) {
            $material_name = MaterialConfig::find( $product_id );
            return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
        }
        DB::beginTransaction();
        try {
            $data['quantity']    = $material_in_details->quantity - $request->quantity;
            $data['rest']        = $material_in_details->rest - $request->quantity;
            $data['total_price'] = $material_in_details->total_price - ( $request->quantity * $material_in_details->unit_price );
            $material_in_details->update( $data );

            // Product return data store
            $returnData                      = new ProductReturn();
            $returnData->product_transfer_id = $material_in_details->id;
            $returnData->type                = 1;
            $returnData->quantity            = $request->quantity;
            $returnData->reason              = $request->reason;
            $returnData->return_by           = Auth::user()->id;
            $returnData->save();

            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Return'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }

    }

    public function purchaseCreate()
    {
        abort_if( Gate::denies( 'material_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $materials = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $units     = Unit::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $employees = Employee::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $suppliers = Supplier::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        return view( 'admin.productPurchase.create', compact( 'employees', 'materials', 'units', 'suppliers' ) );

    }

    public function detailList($id){
        $material_purchase_info = SupplierProduct::where('material_in_id',$id)->first();
        $payments = Payment::where('releted_id',$id)->where('releted_department_id',5)->get();
        $payment_id = $payments->pluck('id');
        $payment_transaction = Transaction::whereIn('payment_id',$payment_id)->where('type',1)->get();
        $bank_info = Bank::get()->keyBy('id');
        $fund_info = Fund::get()->keyBy('id');
        return view('admin.productPurchase.modal.detail',compact('bank_info','fund_info','material_purchase_info','payment_transaction'));
    }

    public function purchase()
    {
        abort_if( Gate::denies( 'product_access' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );

        $materialsPurchased = MaterialIn::with( 'material', 'units' )->where( 'type', 2 )->get()->groupBy( 'material_id' );
        $materials          = MaterialConfig::all();

        return view( 'admin.productPurchase.index', compact( 'materials', 'materialsPurchased' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id )
    {
        //
    }

    public function stock()
    {

    }

    /**
     * @param $request
     */
    private function _checkProductQuantity( $request )
    {
        $total_stock_qty = MaterialIn::where( 'id', $request->id )->first();
        if ( $total_stock_qty > $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }
}
