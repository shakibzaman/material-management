<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MaterialConfig;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MaterialConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('material_config_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $materials = MaterialConfig::where('type',1)->get();
        return view('admin.material-config.index',compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('material_config_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.material-config.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>['required','unique:material_configs'],
        ]);
        MaterialConfig::create($request->all());
        if($request->type==1)
        {
            return redirect()->route('admin.material-config.index');

        }
        if($request->type==2)
        {
            return redirect()->route('admin.product.index');

        }
        if($request->type==3)
        {
            return redirect()->route('admin.color.index');

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
    public function color()
    {
        abort_if(Gate::denies('color_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $materials = MaterialConfig::where('type',3)->get();
        return view('admin.material-config.color-index',compact('materials'));
    }

    public function colorCreate()
    {
        abort_if(Gate::denies('color_config_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.material-config.color');
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
