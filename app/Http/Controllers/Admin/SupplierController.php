<?php

namespace App\Http\Controllers\Admin;

use App\Payment;
use App\Supplier;
use App\MaterialIn;
use App\UserAccount;
use App\SupplierProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
                    $users_account           = UserAccount::where( 'user_id', $due->supplier_id )->first();
                    $update_due['total_due'] = $users_account->total_due - $paid_amt;
                    $users_account->update( $update_due );
                    // User Account update end

                    // Payment data store start
                    $payment                  = new Payment();
                    $payment->amount          = $paid_amt;
                    $payment->payment_process = $request->payment_process;
                    $payment->payment_info    = $request->payment_info;
                    $payment->user_account_id = $users_account->id;
                    $payment->created_by = Auth::user()->id;
                    $payment->save();
                    // Payment data store end

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
                    $payment->payment_info    = $request->payment_info;
                    $payment->user_account_id = $users_account->id;
                    $payment->created_by = Auth::user()->id;
                    $payment->save();
                    // Payment data store end

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
