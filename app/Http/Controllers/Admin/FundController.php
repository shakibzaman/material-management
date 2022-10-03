<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\BankTransaction;
use App\Fund;
use App\FundTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $funds = Fund::all();
        return view('admin.fund.index',compact('funds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.fund.create');
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
            Fund::create($request->all());
            DB::commit();
            return Redirect::back()->with('success', 'Fund details added');
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
        $fund_id = $id;
        return view('admin.fund.deposit',compact('fund_id'));

    }
    public function depositStore(Request $request){
        DB::beginTransaction();
        try{
            $fund_info = Fund::where('id',$request->fund_id)->first();
            $fund['current_balance'] = $fund_info->current_balance + $request->deposit;
            $fund_info->update($fund);

            $transaction = new FundTransaction();
            $transaction->fund_id = $fund_info->id;
            $transaction->type = 2;
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