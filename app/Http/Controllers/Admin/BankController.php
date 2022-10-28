<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Expense;
use App\Fund;
use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::all();
        return view('admin.bank.index',compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bank.create');

    }

    public function widthrowList($id){
        $transactions = Transaction::where('bank_id',$id)
        ->where('source_type',1)->where('type',1)->get();
        $title = 'Widthrow List';

        return view('admin.fund.deposit-list',compact('transactions','title'));

    }

    public function addBankCharge($id){
        $bank_info = Bank::where('id',$id)->first();
        return view('admin.bank.charge',compact('bank_info'));

    }

    public function storeBankCharge(Request $request){
        DB::beginTransaction();
        try{
            $expenseData = new Expense();
            $expenseData->entry_date = date("Y-m-d");;
            $expenseData->amount = $request->charge;
            $expenseData->description = $request->reason;
            $expenseData->expense_category_id  = 16; // Bank expense id 16
            $expenseData->created_by_id   = Auth::user()->id;
            $expenseData->save();

            $transaction = new Transaction();
            $transaction->bank_id = $request->bank_id;
            $transaction->source_type = 2;
            $transaction->type = 1;
            $transaction->destination_type = 1;
            $transaction->amount = $request->charge;
            $transaction->reason = $request->reason;
            $transaction->date = now();
            $transaction->created_by = Auth::user()->id;
            $transaction->save();

            $bank_info = Bank::where('id',$request->bank_id)->first();
            $data['current_balance'] = $bank_info->current_balance -  $request->charge ;
            DB::table('banks')->where('id',$request->bank_id)->update($data);

            DB::commit();
            return Redirect::back()->with('success', 'Bank Service Charge added');
        } catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
            }



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
            $request['created_by'] = Auth::user()->id;
            Bank::create($request->all());
            DB::commit();
            return Redirect::back()->with('success', 'Bank details added');
        }catch (\Exception $e){
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

    public function deposit($id){
        $bank_id = $id;
        return view('admin.bank.deposit',compact('bank_id'));

    }

    public function depositList($id){
        $transactions = Transaction::with('fund','user')->where('bank_id',$id)->where('type',2)->get();
        $title = 'Deposit List';
        return view('admin.fund.deposit-list',compact('transactions','title'));

    }
    public function depositStore(Request $request){
        DB::beginTransaction();
        try{
            if(!is_null($request->fund_id)){
                $fund_info = Fund::where('id',$request->fund_id)->first();
                $fund['current_balance'] = $fund_info->current_balance - $request->deposit;
                $fund_info->update($fund);
            }
            $bank_info = Bank::where('id',$request->bank_id)->first();
            $bank['current_balance'] = $bank_info->current_balance + $request->deposit;
            $bank_info->update($bank);

            $transaction = new Transaction();
            $transaction->bank_id = $bank_info->id;
            $transaction->type = 2;
            $transaction->source_type = 2; // 2 is account 1 is bank
            $transaction->date = now();
            $transaction->source_fund_id = $request->fund_id??0;
            $transaction->destination_fund_id = $request->bank_id;
            $transaction->destination_type = 1;
            $transaction->amount = $request->deposit;
            $transaction->reason = $request->reason;
            $transaction->created_by = Auth::user()->id;

            $transaction->save();
            DB::commit();
            return \redirect()->back()->with('success','Deposit Successful');
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
