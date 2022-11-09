<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Department;
use App\Employee;
use App\EmployeeSalary;
use App\Fund;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = Employee::with('department')->get();

        return view('admin.employee.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('employee_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employee.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:employees|max:255',
            'phone' => 'required',
            'address' => 'required',
            'salary' => 'required',
            'department_id' => 'required',
        ]);
        $request['created_by'] = Auth::user()->id;
        Employee::create($request->all());
        return redirect()->route('admin.employee.index');
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
        abort_if(Gate::denies('employee_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $departments = Department::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $employee = Employee::find($id);
        $employee->load('department');


        return view('admin.employee.edit', compact('employee','departments'));
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
        $employee = Employee::find($id);
        $employee->update($request->all());
        return redirect()->route('admin.employee.index');

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

    public function salary($id){
        $employee = Employee::where('id',$id)->first();
        return view('admin.employee.modal.payment', compact('employee'));

    }

    public function paymentList($id){
        $employee_salaries = EmployeeSalary::where('employee_id',$id)->get();
        return view('admin.employee.salaries-list', compact('employee_salaries'));

    }


    public function paymentStore(Request $request){
        DB::beginTransaction();
        try {
            $date = date_create($request->date);
            $year = date_format($date, "Y");
            $month = date_format($date, "m");

            $employee_salaries = new EmployeeSalary();
            $employee_salaries->employee_id = $request->employee_id;
            $employee_salaries->date = $request->date;
            $employee_salaries->month = $month;
            $employee_salaries->year = $year;
            $employee_salaries->amount = $request->paid_amount;
            $employee_salaries->created_by = Auth::user()->id;
            $employee_salaries->save();

            // Payment data store start
            $payment = new Payment();
            $payment->amount = $request->paid_amount;
            $payment->payment_process = $request->payment_process;
            $payment->releted_department_id = 7; //employee salary
            $payment->payment_info = $request->payment_info;
            $payment->user_account_id = $request->employee_id;
            $payment->releted_id = $request->employee_id;
            $payment->releted_id_type = 4; // salary
            $payment->created_by = Auth::user()->id;
            $payment->save();
            // Payment data store end

            if ($request->payment_process == 'bank') {
                $bank_info = Bank::where('id', $request->payment_type)->first();

                if ($bank_info->current_balance < $request->paid_amount) {
                    DB::rollback();
                    return ['status' => 103, 'message' => 'Sorry Bank amount low'];
                }

                $bank['current_balance'] = $bank_info->current_balance - $request->paid_amount;
                $bank_info->update($bank);

                $transaction = new Transaction();
                $transaction->bank_id = $bank_info->id;
                $transaction->source_type = 1;
                $transaction->type = 1; // 1 is Widthrow
                $transaction->date = $request->date ?? now();
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->paid_amount;
                $transaction->reason = 'Employee Payment';
                $transaction->created_by = Auth::user()->id;

                $transaction->save();

            }
            if ($request->payment_process == 'account') {
                $fund_info = Fund::where('id', $request->payment_type)->first();

                if ($fund_info->current_balance < $request->paid_amount) {
                    DB::rollback();
                    return ['status' => 103, 'message' => 'Sorry Fund amount low'];
                }

                $fund['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                $fund_info->update($fund);

                $transaction = new Transaction();
                $transaction->bank_id = $fund_info->id;
                $transaction->source_type = 2;
                $transaction->type = 1;
                $transaction->date = $request->date ?? now();
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->paid_amount;
                $transaction->reason = 'Employee Payment';
                $transaction->created_by = Auth::user()->id;

                $transaction->save();

            }
            DB::commit();
            return ['status'=>200,'message'=>'Employee payment done'];
        }catch (\Exception$e){
            DB::rollBack();
            return $e->getMessage();
        }

    }

}
