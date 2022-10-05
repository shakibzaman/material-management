<?php

namespace App\Http\Controllers\admin;

use App\Company;
use App\Customer;
use App\Fund;
use App\FundTransaction;
use App\Http\Controllers\Controller;
use App\Order;
use App\Payment;
use App\ProductDelivered;
use App\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::with('account')->get();
        return view('admin.company.index',compact('companies'));
    }

    public function payment($id){

        $customer_detail = Company::where( 'id', $id )->first();
        if ( !$customer_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Customer not founded'];
        }
        $all_dues = ProductDelivered::where( 'company_id', $id )->where( 'due', '>', 0 )->sum( 'due' );

        return view("admin.company.modal.payment",compact('all_dues','customer_detail'));

    }

    public function paymentStore( Request $request )
    {

        if($request->total_amount<$request->paid_amount){
            return ['status' => 103, 'message' => 'Sorry you can not paid more then due'];
        }

        $all_dues = ProductDelivered::where( 'company_id', $request->company_id )->where( 'due', '>', 0 )->get();

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
                        $customer_order              = ProductDelivered::where( 'id', $due->id )->first();
                        $customer_order->update( $order_amount );
                        logger("product Delivered");

                        // Order Amount update end

                        // User Account update start
                        $users_account           = UserAccount::where( 'user_id', $due->customer_id )->where('type',3)->first();
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
                        $payment->releted_id_type = 3;
                        $payment->created_by = Auth::user()->id;
                        $payment->save();
                        // Payment data store end

                    } else {
                        $contentQty = 0;
                        // Order Amount update start
                        $order_amount['paid'] = $due->paid + $paid_amt;
                        $order_amount['due']  = $due->due - $paid_amt;
                        $customer_order              = ProductDelivered::where( 'id', $due->id )->first();
                        $customer_order->update( $order_amount );

                        // Order Amount update end

                        // User Account update start
                        $users_account           = UserAccount::where( 'user_id', $due->company_id )->where('type',3)->first();
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
                        $payment->releted_id_type = 3;
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

                    $transaction = new FundTransaction();
                    $transaction->fund_id = $fund_info->id;
                    $transaction->type = 2;
                    $transaction->amount = $request->paid_amount;
                    $transaction->reason = 'Company Due Payment';
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

    public function returnList($id){
      $return_list = DB::table('product_return')
            ->join('product_transfer','product_return.product_transfer_id','product_transfer.id')
            ->join('material_configs','product_transfer.product_id','material_configs.id')
            ->join('users','product_return.return_by','users.id')
            ->join('transfer','product_transfer.transfer_id','transfer.id')
            ->where('product_return.type',2)
            ->where('transfer.company_id',$id)
            ->select('material_configs.name','product_return.*','users.name AS return_by_user')
            ->get();
        return view( 'admin.company.return-list', compact( 'return_list' ) );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.company.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $company = Company::create($request->all());

        if ( $company ) {
            $supplier_account                  = new UserAccount();
            $supplier_account->type            = 3; // 1 is for Supplier account, 2 is for Customer account , 3 is for Company Account
            $supplier_account->user_id         = $company->id;
            $supplier_account->opening_balance = $request->opening_balance;
            $supplier_account->total_due       = $request->opening_balance;
            $supplier_account->total_paid      = 0;
            $supplier_account->created_by      = Auth::user()->id;
            $supplier_account->save();
        }
        return redirect()->route('admin.company.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
