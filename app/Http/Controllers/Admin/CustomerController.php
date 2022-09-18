<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\MaterialIn;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplierDetail = Customer::where( 'id', $id )->first();
        $materials      = MaterialIn::with( 'material', 'user', 'supplierProduct' )->where( 'supplier_id', $supplierDetail->id )->orderBy( 'id', 'DESC' )->get();
        return view( 'admin.supplier.show', compact( 'materials', 'supplierDetail' ) );
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
        $supplier_detail = Supplier::where( 'id', $id )->first();
        if ( !$supplier_detail ) {
            return ['status' => 105, 'message' => 'Sorry your Supplier not founded'];
        }
        $all_dues = SupplierProduct::where( 'supplier_id', $id )->where( 'due_amount', '>', 0 )->sum( 'due_amount' );
        return view( 'admin.supplier.modal.payment', compact( 'all_dues', 'supplier_detail' ) );

    }
}
