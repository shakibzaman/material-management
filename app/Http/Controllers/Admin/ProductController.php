<?php

namespace App\Http\Controllers\admin;

use DB;
use Gate;
use App\Unit;
use App\Employee;
use App\Supplier;
use App\MaterialIn;
use App\ProductReturn;
use App\MaterialConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        abort_if( Gate::denies( 'product_config_access' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $materials = MaterialConfig::where( 'type', 2 )->get();
        return view( 'admin.product.index', compact( 'materials' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if( Gate::denies( 'product_config_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        return view( 'admin.product.create' );
    }

    /**
     * @param $id
     */
    public function returnList( $id )
    {
        // id is Material-in id
        $materialStock = MaterialIn::where( 'id', $id )->first();

        return view( 'admin.productPurchase.modal.return', compact( 'materialStock' ) );
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function returnStock( Request $request )
    {
        $material_in_details = MaterialIn::where( 'id', $request->id )->first();
        $product_id          = $material_in_details->material_id;

        $check_quantity = $this->_checkProductQuantity( $request );
        if ( !$check_quantity ) {
            $material_name = MaterialConfig::find( $product_id );
            return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
        }
        DB::beginTransaction();
        try {
            $data['quantity']    = $material_in_details->quantity - $request->quantity;
            $data['rest']        = $material_in_details->rest - $request->quantity;
            $data['total_price'] = $material_in_details->total_price - ( $request->quantity * $material_in_details->unit_price );
            $material_in_details->update( $data );

            // Product return data store
            $returnData                      = new ProductReturn();
            $returnData->product_transfer_id = $material_in_details->id;
            $returnData->type                = 1;
            $returnData->quantity            = $request->quantity;
            $returnData->reason              = $request->reason;
            $returnData->return_by           = Auth::user()->id;
            $returnData->save();

            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Return'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }

    }

    public function purchaseCreate()
    {
        abort_if( Gate::denies( 'material_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $materials = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $units     = Unit::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $employees = Employee::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $suppliers = Supplier::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        return view( 'admin.productPurchase.create', compact( 'employees', 'materials', 'units', 'suppliers' ) );

    }

    public function purchase()
    {
        abort_if( Gate::denies( 'product_access' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );

        $materialsPurchased = MaterialIn::with( 'material', 'units' )->where( 'type', 2 )->get()->groupBy( 'material_id' );
        $materials          = MaterialConfig::all();

        return view( 'admin.productPurchase.index', compact( 'materials', 'materialsPurchased' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        //
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

    public function stock()
    {

    }

    /**
     * @param $request
     */
    private function _checkProductQuantity( $request )
    {
        $total_stock_qty = MaterialIn::where( 'id', $request->id )->first();
        if ( $total_stock_qty > $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }
}
