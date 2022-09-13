<?php

namespace App\Http\Controllers\Admin;

use App\Department;
use App\Expense;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\ProductTransfer;
use App\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowroomController extends Controller
{
    public function index(){
        $nettingsData = Transfer::with('company')->whereIn('department_id',[3,4])->get()->groupBy('department_id');
        $transfer_products = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query){
            $query->whereIn('department_id', [3,4]);
        })->get()->groupBy('transfer.department_id');
        $departments = Department::get()->keyBy('id');
        return view('admin.showroom.index',compact('nettingsData','departments','transfer_products'));


    }
    public function store(Request $request){

        $product_id  = $request->product_id;
        $quantity    = $request->quantity;
        $company_id  = $request->company_id;
        $color_id    = $request->color_id;
        $department_id    = $request->showroom_id;

        $check_quantity = $this->_checkDyeingProductQuantity( $request );
        if ( !$check_quantity ) {
            $material_name = MaterialConfig::find( $request->product_id );
            return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
        }
        DB::beginTransaction();
        try {
        $transfer                      = new Transfer();
        $transferData['company_id']    = $company_id;
        $transferData['department_id'] = $department_id;
        $transferData['created_by']    = Auth::user()->id;
        $transferData['date']          = date( "Y-m-d" );
        $storeTransfer                 = $transfer->create( $transferData );
        $transfer_id = $storeTransfer->id;

        $getAllStock = ProductTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company_id, $product_id ) {
            $query->where( 'company_id', '=', $company_id )->where( 'department_id', '=', 2 )->where( 'product_id', '=', $product_id )->where( 'rest_quantity', '>', 0 );
        } )->get();
        $contentQty = $quantity;
        foreach ( $getAllStock as $stock ) {
            $pro_qty = $contentQty;
            if ( $stock->rest_quantity < $contentQty ) {
                $pro_qty    = $stock->rest_quantity;
                $contentQty = $contentQty - $stock->rest_quantity;

                $productTransfer                     = new ProductTransfer();
                $transferProduct['product_id']       = $product_id;
                $transferProduct['quantity']         = $pro_qty;
                $transferProduct['rest_quantity']    = $pro_qty;
                $transferProduct['transfer_id']      = $transfer_id;
                $transferProduct['product_stock_id'] = $stock->id;
                $transferProduct['color_id']         = $color_id;
//                $transferProduct['process_fee']      = '';
                $transferProduct['created_by']       = Auth::user()->id;
                $storeTransfer                       = $productTransfer->create( $transferProduct );

                if ( $storeTransfer ) {
                    // reduce stock quantity
                    $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                    logger( ' Updated rest qty 1 ' . $data['rest_quantity'] );
                }
            } else {
                $contentQty                          = 0;
                $productTransfer                     = new ProductTransfer();
                $transferProduct['product_id']       = $product_id;
                $transferProduct['quantity']         = $pro_qty;
                $transferProduct['rest_quantity']    = $pro_qty;
                $transferProduct['transfer_id']      = $transfer_id;
                $transferProduct['product_stock_id'] = $stock->id;
                $transferProduct['color_id']         = $color_id;
//                $transferProduct['process_fee']      = '';
                $transferProduct['created_by']       = Auth::user()->id;
                $storeTransfer                       = $productTransfer->create( $transferProduct );
                if ( $storeTransfer ) {
                    // reduce stock quantity
                    $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                    logger( ' Updated rest qty 2 ' . $data['rest_quantity'] );
                }
            }

            if ( $contentQty < 1 ) {
                break;
            }

        }
            DB::commit();

            return ['status' => 200, 'message' => 'Successfully Transfer'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    private function _checkDyeingProductQuantity( $request )
    {
        $company         = $request->company_id;
        $product_id      = $request->product_id;
        $total_stock_qty = ProductTransfer::where( 'product_id', $product_id )->with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company ) {
            $query->where( 'company_id', '=', $company )->where( 'department_id', 2 );
        } )->sum( 'rest_quantity' );
        if ( $total_stock_qty >= $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }

    public function show($id){
//        here id is department id
        $transfer_products = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->groupBy('transfer.department_id');


    }
}
