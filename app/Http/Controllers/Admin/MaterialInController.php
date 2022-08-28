<?php

namespace App\Http\Controllers\Admin;

use App\Department;
use App\Employee;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use DB;

class MaterialInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('material_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

       $materialsPurchased = MaterialIn::with('material','units')->where('type',1)->get()->groupBy('material_id');
       $materials = MaterialConfig::all();
       
        return view('admin.material.index', compact('materials','materialsPurchased'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('material_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $materials = MaterialConfig::where('type',1)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $units = Unit::all()->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $employees = Employee::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.material.create', compact('employees','materials','units'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
        $request['created_by'] = Auth::user()->id;
        $request['rest'] = $request->quantity;
        $material = MaterialIn::create($request->all());
        } catch (\Exception $e) {

            return $e->getMessage();
        }
        if($request->type == 1)
        {
            return redirect()->route('admin.material-in.index');
        }
        if($request->type == 2)
        {
            return redirect()->route('admin.product.purchase');
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
        $materials = MaterialIn::with('user','employee','units')->where('material_id',$id)->get();
        return view('admin.material.show', compact('materials'));

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
    public function transfer($id)
    {
        abort_if(Gate::denies('material_transfer'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $departments = Department::all()->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $materials = MaterialIn::with('user','employee','units')->where('material_id',$id)->get();
        return view('admin.material.modal.transfer', compact('materials','departments'));

    }
}
