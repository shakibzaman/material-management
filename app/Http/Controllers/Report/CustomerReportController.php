<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    public function customerOrderReport(){
        $orders = Order::all();
        return view('admin.reports.order.customer-order-report',compact('orders'));
    }
    public function customerOrderReportSearch(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $showroom_id = $request->showroom_id;
        if(is_null($start_date) && is_null($end_date) && is_null($showroom_id)){
            $orders = Order::all();
            return view('admin.reports.order.list', compact('orders'))->render();
        }
        else if(($start_date) && ($end_date) && ($showroom_id == 0)){
            $orders = Order::all();
            return view('admin.reports.order.list', compact('orders'))->render();
        }
        else{
            $orders = Order::where('department_id',$showroom_id)
                ->where('date','>=',$request->start_date)
                ->where('date','<=',$request->end_date)
                ->get();
            return view('admin.reports.order.list', compact('orders'))->render();
        }
    }
}
