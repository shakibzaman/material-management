<?php

namespace App\Http\Controllers\admin;

use App\Company;
use App\Http\Controllers\Controller;
use App\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        return view('admin.company.index',compact('companies'));
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
