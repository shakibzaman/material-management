<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Expense;
use App\Fund;
use App\Http\Controllers\Controller;
use App\Income;
use App\Order;
use App\ProductDelivered;
use App\ProductTransfer;
use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = User::where('id',Auth::user()->id)->first();
        $knitting_product_qty =  ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
            ->whereHas( 'transfer', function ( Builder $query ) {
            $query->where( 'department_id', 1 );
        } )->get()->sum('rest_quantity');

        $knitting_delivery = ProductDelivered::get();

        $dyeing_product_qty =  ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
            ->whereHas( 'transfer', function ( Builder $query ) {
                $query->where( 'department_id', 2 );
            } )->get()->sum('rest_quantity');

        $showroom_n_gonj_product_qty =  ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
            ->whereHas( 'transfer', function ( Builder $query ) {
                $query->where( 'department_id', 3 );
            } )->get()->sum('rest_quantity');

        $showroom_mirpur_product_qty =  ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
            ->whereHas( 'transfer', function ( Builder $query ) {
                $query->where( 'department_id', 4 );
            } )->get()->sum('rest_quantity');

        $expenses = Expense::all();
        $incomes = Income::all();
        $knitting_order = Order::where('department_id',1)->get();
        $n_gonj_order = Order::where('department_id',3)->get();
        $mirpur_order = Order::where('department_id',4)->get();

        $bank_info = Bank::get();
        $bank_id = $bank_info->pluck('id')->toArray();
        $bank_transactions = Transaction::whereIn('bank_id',$bank_id)->get();

        $fund_info = Fund::get();
        $fund_id = $fund_info->pluck('id')->toArray();
        $fund_transactions = Transaction::whereIn('bank_id',$fund_id)->get();
        return view('admin.dashboard.index',compact('fund_transactions','fund_info','bank_transactions','bank_info','incomes','expenses','knitting_delivery','knitting_order','n_gonj_order','mirpur_order','user','knitting_product_qty','dyeing_product_qty','showroom_n_gonj_product_qty','showroom_mirpur_product_qty'));
    }
}
