<?php

namespace App\Http\Controllers\Admin;

use App\StockSet;
use App\MaterialConfig;
use App\StockSetMaterial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class StockSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sets = StockSet::with('color')->get();
        return view( 'admin.dyeing.makeSet.index',compact('sets') );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $materials       = MaterialConfig::where( 'type', 1 )->get();
        $products        = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $colors          = MaterialConfig::where( 'type', 3 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        return view( 'admin.dyeing.makeSet.add-set', compact( 'colors', 'material_key_by', 'products', 'materials' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        DB::beginTransaction();
        try {
            $product_id     = $request->product_id;
            $start_quantity = $request->start_quantity;
            $end_quantity   = $request->end_quantity;
            $color_id       = $request->color_id;

            $stock_id = StockSet::create( $request->all() );

            if ( $stock_id ) {
                foreach ( $request->material_qty as $key => $material ) {
                    if ( $material != null ) {
                        $materialRequest['material_id']       = $key;
                        $materialRequest['material_quantity'] = $material;
                        $materialRequest['stock_set_id']      = $stock_id->id;
                        StockSetMaterial::create( $materialRequest );
                    }
                }
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully set saved'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $set = StockSet::with('color')->where('id',$id)->first();
       $setMaterialDetails = StockSetMaterial::with('material')->where('stock_set_id',$id)->get();
       return view( 'admin.dyeing.makeSet.show', compact( 'setMaterialDetails','set' ) );

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id )
    {
        //
    }

    public function makeSet()
    {
    }

    public function addSet()
    {

    }

    /**
     * @param Request $request
     */
    public function storeSet( Request $request )
    {

    }

}
