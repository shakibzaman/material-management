<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
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
        return view('admin.fund.deposit-list',compact('transactions'));

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
