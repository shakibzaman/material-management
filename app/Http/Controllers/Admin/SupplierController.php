<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Fund;
use App\Payment;
use App\ProductReturn;
use App\Supplier;
use App\MaterialIn;
use App\Transaction;
use App\UserAccount;
use App\SupplierProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpParser\Builder;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = Supplier::with( 'account' )->get();

        return view( 'admin.supplier.index', compact( 'users' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'admin.supplier.create' );
    }

    public function getPaymentType($type){
        if($type == 'bank'){
            $payment_type = Bank::get();
        }
        else if($type == 'account'){
            $payment_type = Fund::get();
        }
        else{
            $payment_type ='';
        }

        return $payment_type;
    }

    public function returnList($id){
        $return_list = DB::table('product_return')
            ->join('material_ins','product_return.product_transfer_id','material_ins.id')
            ->join('material_configs','material_ins.material_id','material_configs.id')
            ->join('users','product_return.return_by','users.id')
            ->where('supplier_id',$id)
            ->where('product_return.type',1)
            ->select('material_configs.name','product_return.*','users.name AS return_by_user')
            ->get();
        return view( 'admin.supplier.return-list', compact( 'return_list' ) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        DB::beginTransaction();
        try {
            $supplier                  = new Supplier();
            $supplier->name            = $request->name;
            $supplier->phone           = $request->phone;
            $supplier->address         = $request->address;
            $supplier->opening_balance = $request->opening_balance;
            $supplier->save();

            $supplier_id = $supplier->id;

            if ( $supplier_id ) {
                $supplier_account                  = new UserAccount();
                $supplier_account->type            = 1; // 1 is for Supplier account, 2 is for Customer account
                $supplier_account->user_id         = $supplier_id;
                $supplier_account->opening_balance = $request->opening_balance;
                $supplier_account->total_due       = $request->opening_balance;
                $supplier_account->total_paid      = 0;
                $supplier_account->created_by      = Auth::user()->id;
                $supplier_account->save();
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Create'];

        } catch ( \Exception $e ) {
            DB::rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $supplierDetail = Supplier::where( 'id', $id )->first();
        $materials      = MaterialIn::with( 'material', 'user', 'supplierProduct' )->where( 'supplier_id', $supplierDetail->id )->orderBy( 'id', 'DESC' )->get();
        return view( 'admin.supplier.show', compact( 'materials', 'supplierDetail' ) );

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

    /**
     * @param $id
     */
    public function payment( $id )
    {
        $supplier_detail = Supplier::where( 'id', $id )->first();
        if ( !$supplier_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Supplier not founded'];
        }
        $all_dues = SupplierProduct::where( 'supplier_id', $id )->where( 'due_amount', '>', 0 )->sum( 'due_amount' );
        return view( 'admin.supplier.modal.payment', compact( 'all_dues', 'supplier_detail' ) );

    }
    public function paymentList( $id )
    {
        $supplier_detail = Supplier::where( 'id', $id )->first();
        if ( !$supplier_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Supplier not founded'];
        }
        $user_account = UserAccount::where('user_id',$id)->where('type',1)->first();
        $all_dues = Payment::with('transaction')->where( 'user_account_id', $user_account->id )->where('releted_id_type',2)->get();
        return view( 'admin.supplier.modal.payment', compact( 'all_dues', 'supplier_detail' ) );

    }
    public function paymentAllList( $id )
    {
        $supplier_detail = Supplier::where( 'id', $id )->first();
        if ( !$supplier_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Supplier not founded'];
        }
        $user_account = UserAccount::where('user_id',$id)->where('type',1)->first();
        $user_account_id = $user_account->id;
        $transactions = Transaction::with('payment','user')
            ->whereHas('payment',function (\Illuminate\Database\Eloquent\Builder $query) use ($user_account_id){
            $query->where('user_account_id', $user_account_id);
        })->get();
        $bank_info = Bank::get()->keyBy('id');
        $fund_info = Fund::get()->keyBy('id');

        return view( 'admin.supplier.payment-all-list', compact( 'transactions', 'supplier_detail','bank_info','fund_info' ) );

    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function paymentStore( Request $request )
    {
        if($request->total_amount<$request->paid_amount){
            return ['status' => 103, 'message' => 'Sorry you can not paid more then due'];
        }
        $all_dues = SupplierProduct::where( 'supplier_id', $request->supplier_id )->where( 'due_amount', '>', 0 )->get();

        $contentQty = $request->paid_amount;
        DB::beginTransaction();
        try {
            foreach ( $all_dues as $due ) {
                $paid_amt = $contentQty;
                if ( $due->due_amount < $contentQty ) {
                    $paid_amt   = $due->due_amount;
                    $contentQty = $contentQty - $due->due_amount;

                    // Supplier Amount update start
                    $supplier_amount['paid_amount'] = $due->paid_amount + $paid_amt;
                    $supplier_amount['due_amount']  = $due->due_amount - $paid_amt;
                    $supplier_product               = SupplierProduct::where( 'id', $due->id )->first();
                    $supplier_product->update( $supplier_amount );

                    // Supplier Amount update end

                    // User Account update start
                    $users_account           = UserAccount::where( 'user_id', $due->supplier_id )->where('type',1)->first();
                    $update_due['total_due'] = $users_account->total_due - $paid_amt;
                    $users_account->update( $update_due );
                    // User Account update end

                    // Payment data store start
                    $payment                  = new Payment();
                    $payment->amount          = $paid_amt;
                    $payment->payment_process = $request->payment_process;
                    $payment->payment_info    = $request->payment_info;
                    $payment->user_account_id = $users_account->id;
                    $payment->releted_id = $due->id;
                    $payment->releted_id_type = 2;
                    $payment->created_by = Auth::user()->id;
                    $payment->save();
                    // Payment data store end

                    if($request->payment_process == 'bank'){
                        $bank_info = Bank::where('id',$request->payment_type)->first();
                        $bank['current_balance'] = $bank_info->current_balance - $paid_amt;
                        $bank_info->update($bank);

                        $transaction = new Transaction();
                        $transaction->bank_id = $bank_info->id;
                        $transaction->source_type = 1;
                        $transaction->date = $request->date ?? now();
                        $transaction->type = 1; // 1 is Widthrow
                        $transaction->destination_fund_id = 0;
                        $transaction->destination_type = 0;
                        $transaction->payment_id = $payment->id;
                        $transaction->amount = $paid_amt;
                        $transaction->reason = 'Supplier Payment';
                        $transaction->created_by = Auth::user()->id;

                        $transaction->save();

                    }
                    if($request->payment_process == 'account'){
                        $fund_info = Fund::where('id',$request->payment_type)->first();
                        $fund['current_balance'] = $fund_info->current_balance - $paid_amt;
                        $fund_info->update($fund);

                        $transaction = new Transaction();
                        $transaction->bank_id = $fund_info->id;
                        $transaction->source_type = 2;
                        $transaction->date = $request->date ?? now();
                        $transaction->type = 1;
                        $transaction->destination_fund_id = 0;
                        $transaction->destination_type = 0;
                        $transaction->payment_id = $payment->id;
                        $transaction->amount = $paid_amt;
                        $transaction->reason = 'Supplier Payment';
                        $transaction->created_by = Auth::user()->id;

                        $transaction->save();

                    }

                } else {
                    $contentQty = 0;
                    // Supplier Amount update start
                    $supplier_amount['paid_amount'] = $due->paid_amount + $paid_amt;
                    $supplier_amount['due_amount']  = $due->due_amount - $paid_amt;
                    $supplier_product               = SupplierProduct::where( 'id', $due->id )->first();
                    $supplier_product->update( $supplier_amount );

                    // Supplier Amount update end

                    // User Account update start
                    $users_account           = UserAccount::where( 'user_id', $due->supplier_id )->first();
                    $update_due['total_due'] = $users_account->total_due - $paid_amt;
                    $users_account->update( $update_due );
                    // User Account update end

                    // Payment data store start
                    $payment                  = new Payment();
                    $payment->amount          = $paid_amt;
                    $payment->payment_process = $request->payment_process;
                    $payment->releted_department_id = 5;
                    $payment->payment_info    = $request->payment_info;
                    $payment->user_account_id = $users_account->id;
                    $payment->releted_id = $due->id;
                    $payment->releted_id_type = 2;
                    $payment->created_by = Auth::user()->id;
                    $payment->save();
                    // Payment data store end

                    if($request->payment_process == 'bank'){
                        $bank_info = Bank::where('id',$request->payment_type)->first();
                        $bank['current_balance'] = $bank_info->current_balance - $request->paid_amount;
                        $bank_info->update($bank);

                        $transaction = new Transaction();
                        $transaction->bank_id = $bank_info->id;
                        $transaction->source_type = 1;
                        $transaction->type = 1; // 1 is Widthrow
                        $transaction->date = $request->date ?? now();
                        $transaction->payment_id = $payment->id;
                        $transaction->amount = $request->paid_amount;
                        $transaction->reason = 'Supplier Payment';
                        $transaction->created_by = Auth::user()->id;

                        $transaction->save();

                    }
                    if($request->payment_process == 'account'){
                        $fund_info = Fund::where('id',$request->payment_type)->first();
                        $fund['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                        $fund_info->update($fund);

                        $transaction = new Transaction();
                        $transaction->bank_id = $fund_info->id;
                        $transaction->source_type = 2;
                        $transaction->type = 1;
                        $transaction->date = $request->date ?? now();
                        $transaction->payment_id = $payment->id;
                        $transaction->amount = $request->paid_amount;
                        $transaction->reason = 'Supplier Payment';
                        $transaction->created_by = Auth::user()->id;

                        $transaction->save();

                    }

                }

                if ( $contentQty < 1 ) {
                    break;
                }

            }

            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Payment Done'];

        } catch ( \Exception $e ) {
            DB::rollback();
            return $e;
        }
    }
}
