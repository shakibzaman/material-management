<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Customer;
use App\Fund;
use App\Http\Controllers\Controller;
use App\MaterialIn;
use App\Order;
use App\Payment;
use App\SupplierProduct;
use App\Transaction;
use App\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Customer::with( 'account' )->get();

        return view( 'admin.customer.index', compact( 'users' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'admin.customer.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $supplier                  = new Customer();
            $supplier->name            = $request->name;
            $supplier->phone           = $request->phone;
            $supplier->address         = $request->address;
            $supplier->opening_balance = $request->opening_balance;
            $supplier->save();

            $supplier_id = $supplier->id;

            if ( $supplier_id ) {
                $supplier_account                  = new UserAccount();
                $supplier_account->type            = 2; // 1 is for Supplier account, 2 is for Customer account
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerDetail = Customer::where( 'id', $id )->first();
        $orders      = Order::where( 'customer_id', $id )->get();
        return view( 'admin.customer.show', compact( 'customerDetail', 'orders' ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function payment( $id )
    {
        $customer_detail = Customer::where( 'id', $id )->first();
        if ( !$customer_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Customer not founded'];
        }
        $all_dues = Order::where( 'customer_id', $id )->where( 'due', '>', 0 )->sum( 'due' );
        return view( 'admin.customer.modal.payment', compact( 'all_dues', 'customer_detail' ) );

    }

    public function paymentStore( Request $request )
    {
        if($request->total_amount<$request->paid_amount){
            return ['status' => 103, 'message' => 'Sorry you can not paid more then due'];
        }
        $all_dues = Order::where( 'customer_id', $request->customer_id )->where( 'due', '>', 0 )->get();
        if(count($all_dues)>0){
            $contentQty = $request->paid_amount;
            DB::beginTransaction();
            try {
                foreach ( $all_dues as $due ) {
                    $paid_amt = $contentQty;
                    if ( $due->due < $contentQty ) {
                        $paid_amt   = $due->due;
                        $contentQty = $contentQty - $due->due;

                        // Order Amount update start
                        $order_amount['paid'] = $due->paid + $paid_amt;
                        $order_amount['due']  = $due->due - $paid_amt;
                        $customer_order              = Order::where( 'id', $due->id )->first();
                        $customer_order->update( $order_amount );

                        // Order Amount update end

                        // User Account update start
                        $users_account           = UserAccount::where( 'user_id', $due->customer_id )->where('type',2)->first();
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
                        // Order Amount update start
                        $order_amount['paid'] = $due->paid + $paid_amt;
                        $order_amount['due']  = $due->due - $paid_amt;
                        $customer_order              = Order::where( 'id', $due->id )->first();
                        $customer_order->update( $order_amount );

                        // Order Amount update end

                        // User Account update start
                        $users_account           = UserAccount::where( 'user_id', $due->customer_id )->where('type',2)->first();
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

                if($request->payment_process == 'cash'){
                    $fund_info = Fund::where('id',1)->first();
                    $bank['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                    $fund_info->update($bank);

                    $transaction = new Transaction();
                    $transaction->bank_id = $fund_info->id;
                    $transaction->source_type = 2; // 2 is account 1 is bank
                    $transaction->type = 2;
                    $transaction->amount = $request->paid_amount;
                    $transaction->reason = 'Order Due Payment';
                    $transaction->created_by = Auth::user()->id;
                    $transaction->save();

                }
                DB::commit();
                return ['status' => 200, 'message' => 'Successfully Payment Done for Customer'];

            } catch ( \Exception $e ) {
                DB::rollback();
                return $e;
            }
        }
    }

}
