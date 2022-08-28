<?php

namespace App\Http\Controllers\admin;

use App\Employee;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\Unit;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('product_config_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $materials = MaterialConfig::where('type',2)->get();
        return view('admin.product.index',compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('product_config_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.product.create');
    }

    public function purchaseCreate()
    {
        abort_if(Gate::denies('material_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $materials = MaterialConfig::where('type',2)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $units = Unit::all()->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $employees = Employee::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.productPurchase.create', compact('employees','materials','units'));

    }
    public function purchase()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

       $materialsPurchased = MaterialIn::with('material','units')->where('type',2)->get()->groupBy('material_id');
       $materials = MaterialConfig::all();
       
        return view('admin.productPurchase.index', compact('materials','materialsPurchased'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    public function stock()
    {

    }
}
