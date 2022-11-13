<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Department;
use App\Expense;
use App\ExpenseCategory;
use App\Fund;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyExpenseRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Payment;
use App\Transaction;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $expense_categories = ExpenseCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $expenses = Expense::all();
        return view('admin.expenses.index', compact('expense_categories','expenses'));
    }

    public function create()
    {
        abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense_categories = ExpenseCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.expenses.create', compact('expense_categories','departments'));
    }

    public function expenseSearch(Request $request){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $expense_category_id = $request->expense_category_id;
        if(is_null($start_date) && is_null($end_date) && is_null($expense_category_id)){
            $expenses = Expense::get();
            return view('admin.expenses.list', compact('expenses'))->render();
        }
        else if($start_date && $end_date && is_null($expense_category_id)){
            $expenses = Expense::get();
            return view('admin.expenses.list', compact('expenses'))->render();
        }
        else{
            $expenses = Expense::where('expense_category_id',$expense_category_id)
                ->where('entry_date','>=',$request->start_date)
                ->where('entry_date','<=',$request->end_date)
                ->get();
            return view('admin.expenses.list', compact('expenses'))->render();
        }

    }

    public function store(StoreExpenseRequest $request)
    {
        DB::beginTransaction();
        try{
        $expense = Expense::create($request->all());
        if($expense){
            // Payment data store start
            $payment = new Payment();

            $payment->amount          = $request->amount;
            $payment->payment_process = $request->payment_process;
            $payment->payment_info    = $request->payment_info;
            $payment->releted_department_id = $request->department_id;
            $payment->releted_id = $expense->id;
            $payment->releted_id_type = 5;
            $payment->created_by = Auth::user()->id;
            $payment->save();

            // Payment data store end

            if ($request->payment_process == 'bank') {
                $bank_info = Bank::where('id', $request->payment_type)->first();

                if ($bank_info->current_balance < $request->amount) {
                    DB::rollback();
                    return ['status' => 103, 'message' => 'Sorry Bank amount low'];
                }

                $bank['current_balance'] = $bank_info->current_balance - $request->amount;
                $bank_info->update($bank);

                $transaction = new Transaction();
                $transaction->bank_id = $bank_info->id;
                $transaction->source_type = 1;
                $transaction->type = 1; // 1 is Widthrow
                $transaction->date = $request->date ?? now();
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->amount;
                $transaction->reason = $request->description;
                $transaction->created_by = Auth::user()->id;

                $transaction->save();

            }
            if ($request->payment_process == 'account') {
                $fund_info = Fund::where('id', $request->payment_type)->first();
                if ($fund_info->current_balance < $request->amount) {
                    DB::rollback();
                    return ['status' => 103, 'message' => 'Sorry Fund amount low'];
                }

                $fund['current_balance'] = $fund_info->current_balance - $request->amount;
                $fund_info->update($fund);

                $transaction = new Transaction();
                $transaction->bank_id = $fund_info->id;
                $transaction->source_type = 2;
                $transaction->type = 1;
                $transaction->date = $request->date ?? now();
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->amount;
                $transaction->reason = $request->description;
                $transaction->created_by = Auth::user()->id;

                $transaction->save();

            }
        }
        DB::commit();
        return ['status'=>200,'message'=>'Expense added successfully'];
        }catch (Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }


//        return redirect()->route('admin.expenses.index');
    }

    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense_categories = ExpenseCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expense->load('expense_category', 'created_by','department');

        return view('admin.expenses.edit', compact('expense_categories', 'expense','departments'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->all());

        return redirect()->route('admin.expenses.index');
    }

    public function show(Expense $expense)
    {
        abort_if(Gate::denies('expense_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->load('expense_category', 'created_by','department');

        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('expense_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpenseRequest $request)
    {
        Expense::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
