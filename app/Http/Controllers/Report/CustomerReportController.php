<?php

namespace App\Http\Controllers\Report;

use App\Company;
use App\Customer;
use App\Department;
use App\Expense;
use App\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\invoice;
use App\Order;
use App\OrderDetail;
use App\ProductDelivered;
use App\ProductTransfer;
use App\Supplier;
use App\SupplierProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use DB;

class CustomerReportController extends Controller
{
    public function customerOrderReport(){
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        $orders = Order::all();
        return view('admin.reports.order.customer-order-report',compact('orders','customers'));
    }
    public function customerOrderReportSearch(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $showroom_id = $request->showroom_id;
        $customer_id = $request->customer_id;

        $allSearchInputs = array();
        if ($showroom_id) $allSearchInputs['department_id'] = $showroom_id;
        if ($customer_id) $allSearchInputs['customer_id'] = $customer_id;

        if(is_null($start_date) && is_null($end_date) && is_null($showroom_id)){
            $orders = Order::all();
            return view('admin.reports.order.list', compact('orders'))->render();
        }
        else{
            $orders = Order::where('date','>=',$request->start_date)
                ->where('date','<=',$request->end_date)
                ->where($allSearchInputs)
                ->get();
            return view('admin.reports.order.list', compact('orders'))->render();
        }
    }

    public function expenseReport(){
        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $expense_categories = ExpenseCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $expenses = Expense::all();
        return view('admin.reports.expense_report.expense-report', compact('expense_categories','expenses','departments'));
    }
    public function expenseReportSearch(Request $request){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $expense_category_id = $request->expense_category_id;
        $department_id = $request->department_id;

        $allSearchInputs = array();
        if ($department_id) $allSearchInputs['department_id'] = $department_id;
        if ($expense_category_id) $allSearchInputs['expense_category_id'] = $expense_category_id;

        if(is_null($start_date) && is_null($end_date) && is_null($expense_category_id)){
            $expenses = Expense::get();
            return view('admin.reports.expense_report.list', compact('expenses'))->render();

        }else{
            $expenses = Expense::where('entry_date','>=',$request->start_date)
                ->where('entry_date','<=',$request->end_date)
                ->where($allSearchInputs)
                ->get();

            return view('admin.reports.expense_report.list', compact('expenses'))->render();

        }

    }

    public function knittingInReport(){
        $companies = Company::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $knitting_in = ProductTransfer::with('transfer','product')->whereHas('transfer', function (Builder $query){
             $query->where('department_id', 1);
         })->get();

        return view('admin.reports.knitting.knitting-report', compact('companies','knitting_in'));
    }

    public function knittingReportSearch(Request $request){
        $start_date = $request->start_date;
        $end_date = date('Y-m-d', strtotime($request->end_date . ' +1 day'));
        $company_id = $request->company_id;
        $type = $request->type;

        $allSearchInputs = array();
        if ($company_id) $allSearchInputs['company_id'] = $company_id;

        if(is_null($start_date) && is_null($end_date) && is_null($company_id) && is_null($type)) {

            $knitting_in = ProductTransfer::with('transfer', 'product')->whereHas('transfer', function (Builder $query) {
                $query->where('department_id', 1);
            })->get();
            return view('admin.reports.knitting.list', compact('knitting_in'))->render();
        }else{
            if($type == 1 || $type == 0){
                if(is_null($company_id)){
                    $knitting_in = ProductTransfer::with('transfer', 'product')
                        ->where('created_at','>=',$request->start_date)
                        ->where('created_at','<=',$end_date)
                        ->whereHas('transfer', function (Builder $query) use ($company_id){
                            $query->where('department_id', 1);
                        })->get();
                }
                else{
                    $knitting_in = ProductTransfer::with('transfer', 'product')
                        ->where('created_at','>=',$request->start_date)
                        ->where('created_at','<=',$end_date)
                        ->whereHas('transfer', function (Builder $query) use ($company_id){
                            $query->where('department_id', 1)->where('company_id',$company_id);
                        })->get();
                }

            }
            if($type == 2){
                if(is_null($company_id)){
                    $knitting_in = ProductTransfer::with('transfer', 'product')
                        ->where('created_at','>=',$request->start_date)
                        ->where('created_at','<=',$end_date)
                        ->whereHas('transfer', function (Builder $query) use ($company_id){
                            $query->where('department_id', 2);
                        })->get();
                }else{
                    $knitting_in = ProductTransfer::with('transfer', 'product')
                        ->where('created_at','>=',$request->start_date)
                        ->where('created_at','<=',$end_date)
                        ->whereHas('transfer', function (Builder $query) use ($company_id){
                            $query->where('department_id', 2)->where('company_id',$company_id);
                        })->get();
                }

            }
            if($type == 3){
                if(is_null($company_id)){
                    $knitting_in = ProductDelivered::with('product','company')
                        ->where('date','>=',$request->start_date)
                        ->where('date','<=',$request->end_date)
                        ->get();
                }else{
                    $knitting_in = ProductDelivered::with('product','company')
                        ->where('date','>=',$request->start_date)
                        ->where('date','<=',$request->end_date)
                        ->where('company_id',$company_id)
                        ->get();
                }
            }
            return view('admin.reports.knitting.list', compact('knitting_in','type'))->render();

        }
    }

    public function dyeingReport(){
        $departments = Department::whereIn('id',[3,4])->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $transfer_products = DB::table('product_transfer')->join('transfer','product_transfer.transfer_id','transfer.id')
            ->join('material_configs','product_transfer.color_id','material_configs.id')
            ->join('departments','transfer.department_id','departments.id')
            ->whereIn('department_id',[3,4])
            ->select('product_transfer.*','material_configs.name AS color_name','departments.name AS department_name')->get();

        return view('admin.reports.dyeing.dyeing-report', compact('transfer_products','departments'));
    }
    public function dyeingReportSearch(Request $request){

        $start_date = $request->start_date;
        $end_date = date('Y-m-d', strtotime($request->end_date . ' +1 day'));
        $department_id = $request->department_id;
        $type = $request->type;

        $allSearchInputs = array();
        if ($department_id) $allSearchInputs['department_id'] = $department_id;
        if ($type != null) $allSearchInputs['process_type'] = $type;

        $transfer_products = DB::table('product_transfer')->join('transfer','product_transfer.transfer_id','transfer.id')
            ->join('material_configs','product_transfer.color_id','material_configs.id')
            ->join('departments','transfer.department_id','departments.id')
            ->where('product_transfer.created_at','>=',$request->start_date)
            ->where('product_transfer.created_at','<=',$end_date)
            ->where($allSearchInputs)
            ->select('product_transfer.*','material_configs.name AS color_name','departments.name AS department_name')->get();
        return view('admin.reports.dyeing.list', compact('transfer_products'))->render();
    }

    public function supplierProductReport()
    {
        $suppliers = Supplier::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $invoices = invoice::join('suppliers','invoices.supplier_id','suppliers.id')
            ->select('invoices.*','suppliers.name as supplier_name')->get();
        return view('admin.reports.supplier.product-report', compact('invoices','suppliers'));

    }

    public function supplierProductReportSearch(Request $request){
        $start_date = $request->start_date;
        $end_date = date('Y-m-d', strtotime($request->end_date . ' +1 day'));
        $supplier_id = $request->supplier_id;
        $type = $request->type;

        $allSearchInputs = array();
        if ($supplier_id) $allSearchInputs['supplier_id'] = $supplier_id;
        if ($type != null) $allSearchInputs['type'] = $type;

        $invoices = invoice::join('suppliers','invoices.supplier_id','suppliers.id')
            ->where('invoices.date','>=',$request->start_date)
            ->where('invoices.date','<=',$request->end_date)
            ->where($allSearchInputs)
            ->select('invoices.*','suppliers.name as supplier_name')->get();
        return view('admin.reports.supplier.list', compact('invoices'))->render();
    }

    public function profitOrderReport(){
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        $orders = Order::all();
        $order_details = OrderDetail::join('products','order_details.product_id','products.id')
            ->select('order_details.*','products.process_costing')
            ->get()->groupBy('order_id');
        return view('admin.reports.order.profit-order-report',compact('orders','customers','order_details'));
    }

    public function profitOrderReportSearch(Request $request){
        $showroom_id = $request->showroom_id;
        $customer_id = $request->customer_id;

        $allSearchInputs = array();
        if ($showroom_id) $allSearchInputs['department_id'] = $showroom_id;
        if ($customer_id) $allSearchInputs['customer_id'] = $customer_id;

       $orders = Order::where('date','>=',$request->start_date)
            ->where('date','<=',$request->end_date)
            ->where($allSearchInputs)
            ->get();
        $order_details = OrderDetail::join('products','order_details.product_id','products.id')
            ->select('order_details.*','products.process_costing')
            ->get()->groupBy('order_id');
            return view('admin.reports.order.profit-order-list', compact('orders','order_details'))->render();
    }


}
