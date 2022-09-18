<?php

namespace App\Http\Controllers\admin;

use Gate;
use App\User;
use App\Company;
use App\Expense;
use App\Customer;
use App\Transfer;
use App\MaterialIn;
use App\ProductReturn;
use App\MaterialConfig;
use App\ProductTransfer;
use App\MaterialTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;

class NeetingController extends Controller
{
    public function index()
    {
        abort_if( Gate::denies( 'material_create' ), Response::HTTP_FORBIDDEN, '403 Forbidden' );
        $nettingsData      = Transfer::with( 'company' )->where( 'department_id', 1 )->get()->groupBy( 'company_id' );
        $transfer_products = ProductTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) {
            $query->where( 'department_id', '=', 1 );
        } )->get()->groupBy( 'transfer.company_id' );

        $transfer_materials = MaterialTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) {
            $query->where( 'department_id', '=', 1 );
        } )->get()->groupBy( 'transfer.company_id' );
        $companyList = Company::get()->keyBy( 'id' );
        return view( 'admin.neeting.index', compact( 'nettingsData', 'transfer_products', 'transfer_materials', 'companyList' ) );

    }

    /**
     * @param $id
     */
    public function dyeingInProduct( $id )
    {
        $company_id = $id;
        $materials  = ProductTransfer::with( 'transfer', 'product' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->pluck( 'product.name', 'product.id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->groupBy( 'product_id' );
        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        $knittingStock   = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
                                                            ->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
                                                                $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
                                                            } )->get();
        return view( 'admin.neeting.stock-in-dyeing', compact( 'company_id', 'materials', 'knittingStock', 'material_key_by', 'rest_quantity' ) );
    }

    /**
     * @param $id
     */
    public function knittingSellProduct( $id )
    {
        $company_id = $id;
        $materials  = ProductTransfer::with( 'transfer', 'product' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->pluck( 'product.name', 'product.id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->groupBy( 'product_id' );
        $customers       = Customer::get()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        $knittingStock   = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
                                                            ->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
                                                                $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
                                                            } )->get();
        return view( 'admin.neeting.stock-sell-knitting', compact( 'customers', 'company_id', 'materials', 'knittingStock', 'material_key_by', 'rest_quantity' ) );
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function dyeingTransferProduct( Request $request )
    {
        $check_quantity = $this->checkKneetingProductQuantity( $request );
        $product_id     = $request->product_id;
        if ( !$check_quantity ) {
            $material_name = MaterialConfig::find( $product_id );
            return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
        }
        return $this->_dyeingTransferStore( $request );
    }

    /**
     * @param  $request
     * @return mixed
     */
    private function _dyeingTransferStore( $request )
    {
        $product_id         = $request->product_id;
        $quantity           = $request->quantity;
        $company_id         = $request->company_id;
        $process_fee        = $request->process_fee;
        $check_company_type = Company::where( 'id', $company_id )->first();

        DB::beginTransaction();
        try {
            $transfer                      = new Transfer();
            $transferData['company_id']    = $company_id;
            $transferData['department_id'] = 2;
            $transferData['created_by']    = Auth::user()->id;
            $transferData['date']          = date( "Y-m-d" );
            $storeTransfer                 = $transfer->create( $transferData );

            $transfer_id = $storeTransfer->id;
            if ( $check_company_type->type == 1 ) {
                $getAllStock = MaterialIn::where( 'material_id', $product_id )->where( 'rest', '>', 0 )->get();

                $getAllStock = ProductTransfer::with( 'transfer' )
                    ->where( 'product_id', '=', $product_id )->where( 'rest_quantity', '>', 0 )
                    ->whereHas( 'transfer', function ( Builder $query ) use ( $company_id, $product_id ) {
                        $query->where( 'company_id', '=', $company_id )->where( 'department_id', 1 );
                    } )->get();

                $contentQty = $quantity;
                foreach ( $getAllStock as $stock ) {
                    $pro_qty = $contentQty;
                    if ( $stock->rest_quantity < $contentQty ) {
                        $pro_qty    = $stock->rest_quantity;
                        $contentQty = $contentQty - $stock->rest;

                        $productTransfer                     = new ProductTransfer();
                        $transferProduct['product_id']       = $product_id;
                        $transferProduct['quantity']         = $pro_qty;
                        $transferProduct['rest_quantity']    = $pro_qty;
                        $transferProduct['transfer_id']      = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['process_fee']      = $process_fee;
                        $transferProduct['created_by']       = Auth::user()->id;
                        $storeTransfer                       = $productTransfer->create( $transferProduct );

                        if ( $storeTransfer ) {
                            // reduce stock quantity
                            $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                            return $data;
                            DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );

                            // Add expense

                            $expense                      = new Expense();
                            $expense->entry_date          = date( "Y-m-d" );
                            $expense->amount              = ( $pro_qty * $stock->unit_price );
                            $expense->description         = "Product Process Costing for Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id       = 1;
                            $expense->created_by_id       = Auth::user()->id;
                            $expense->material_id         = $stock->product_id;
                            $expense->transfer_id         = $transfer_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                        }
                    } else {
                        $contentQty                          = 0;
                        $productTransfer                     = new ProductTransfer();
                        $transferProduct['product_id']       = $product_id;
                        $transferProduct['quantity']         = $pro_qty;
                        $transferProduct['rest_quantity']    = $pro_qty;
                        $transferProduct['transfer_id']      = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['process_fee']      = $process_fee;
                        $transferProduct['created_by']       = Auth::user()->id;

                        $storeTransfer = $productTransfer->create( $transferProduct );
                        if ( $storeTransfer ) {
                            // reduce stock quantity
                            $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                            DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );

                            // Add expense

                            $expense                      = new Expense();
                            $expense->entry_date          = date( "Y-m-d" );
                            $expense->amount              = ( $pro_qty * $stock->unit_price );
                            $expense->description         = "Product Process Costing for Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id       = 1;
                            $expense->created_by_id       = Auth::user()->id;
                            $expense->material_id         = $stock->product_id;
                            $expense->transfer_id         = $transfer_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                        }
                    }

                    if ( $contentQty < 1 ) {
                        break;
                    }

                }
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Transfer To Dyeing'];

        } catch ( \Exception $e ) {
            DB::rollback();
            return $e;
        }
    }

    /**
     * @param $id
     */
    public function returnList( $id )
    {
        //        id is company id
        $knittingStock = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )
                                                          ->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
                                                              $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
                                                          } )->get();
        $type          = 1;
        $company_id    = $id;
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->groupBy( 'product_id' );

        $product_list = ProductTransfer::with( 'transfer', 'product' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 1 );
        } )->get()->pluck( 'product.name', 'product.id' )->prepend( trans( 'global.pleaseSelect' ), '' );

        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        return view( 'admin.neeting.modal.return-to-stock', compact( 'product_list', 'material_key_by', 'knittingStock', 'company_id', 'type', 'rest_quantity' ) );
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function returnStock( Request $request )
    {
        $company_id     = $request->company_id;
        $product_id     = $request->product_id;
        $company_type   = Company::where( 'id', $company_id )->first();
        $total_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->where( 'product_id', $product_id )
                                                           ->whereHas( 'transfer', function ( Builder $query ) use ( $company_id ) {
                                                               $query->where( 'company_id', '=', $company_id )->where( 'department_id', 1 );
                                                           } )->get()->sum( 'rest_quantity' );

        if ( $total_quantity < $request->transfer_stock ) {
            return ['status' => 103, 'message' => 'Sorry you can\'t return more then you have'];
        }

        DB::beginTransaction();
        try {
            $getAllStock = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->where( 'product_id', $product_id )
                                                            ->whereHas( 'transfer', function ( Builder $query ) use ( $company_id ) {
                                                                $query->where( 'company_id', '=', $company_id )->where( 'department_id', 1 );
                                                            } )->get();

            $contentQty = $request->transfer_stock;
            foreach ( $getAllStock as $stock ) {
                $pro_qty = $contentQty;
                if ( $stock->rest_quantity < $contentQty ) {
                    $pro_qty    = $stock->rest_quantity;
                    $contentQty = $contentQty - $stock->rest_quantity;

                    // Product Transfer update start
                    $productTransfer['quantity']      = $stock->quantity - $pro_qty;
                    $productTransfer['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    $product_transfer                 = ProductTransfer::where( 'id', $stock->id )->first();
                    $product_transfer->update( $productTransfer );

                    // Product Transfer update end

                    if ( $request->type == 1 && $company_type->type == 1 ) {
                        // Material Stock update start
                        $orginal_stock                 = MaterialIn::where( 'id', $stock->product_stock_id )->first();
                        $return_stock                  = $orginal_stock->rest + $pro_qty;
                        $update_material_stock['rest'] = $return_stock;
                        $orginal_stock->update( $update_material_stock );
                        // Material Stock update end
                    }

                    // Product return data store
                    $returnData                      = new ProductReturn();
                    $returnData->product_transfer_id = $stock->id;
                    $returnData->type                = 2;
                    $returnData->quantity            = $pro_qty;
                    $returnData->reason              = $request->reason;
                    $returnData->return_by           = Auth::user()->id;
                    $returnData->save();

                } else {
                    $contentQty = 0;
                    // Product Transfer update start
                    $productTransfer['quantity']      = $stock->quantity - $pro_qty;
                    $productTransfer['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                    $product_transfer                 = ProductTransfer::where( 'id', $stock->id )->first();
                    $product_transfer->update( $productTransfer );
                    // Product Transfer update end

                    if ( $request->type == 1 && $company_type->type == 1 ) {
                        // Material Stock update start
                        $orginal_stock                 = MaterialIn::where( 'id', $stock->product_stock_id )->first();
                        $return_stock                  = $orginal_stock->rest + $pro_qty;
                        $update_material_stock['rest'] = $return_stock;
                        $orginal_stock->update( $update_material_stock );
                        // Material Stock update end
                    }

                    // Product return data store
                    $returnData                      = new ProductReturn();
                    $returnData->product_transfer_id = $stock->id;
                    $returnData->type                = 2;
                    $returnData->quantity            = $pro_qty;
                    $returnData->reason              = $request->reason;
                    $returnData->return_by           = Auth::user()->id;
                    $returnData->save();

                }
                if ( $contentQty < 1 ) {
                    break;
                }
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully returned'];

        } catch ( \Exception $e ) {
            DB::rollback();
            return $e;
        }
    }

    /**
     * @param $id
     */
    public function transferList( $id )
    {
        $nettingsData      = Transfer::with( 'company' )->where( 'department_id', 1 )->where( 'company_id', '=', $id )->get();
        $transfer_products = ProductTransfer::with( 'transfer', 'product' )->get()->groupBy( 'transfer_id' );
        return view( 'admin.neeting.company.transfer-list', compact( 'nettingsData', 'transfer_products' ) );

    }

    /**
     * @param $id
     */
    public function transferOtherShow( $id )
    {
        $transfer           = Transfer::with( 'company' )->where( 'id', $id )->first();
        $transfer_products  = ProductTransfer::with( 'product' )->where( 'transfer_id', '=', $id )->get();
        $transfer_materials = MaterialTransfer::with( 'detail', 'material' )->where( 'transfer_id', '=', $id )->get();
        return view( 'admin.neeting.show-other', compact( 'transfer', 'transfer_products', 'transfer_materials' ) );

    }

    /**
     * @param $id
     */
    public function transferShow( $id )
    {
        $transfer          = Transfer::with( 'company' )->where( 'id', $id )->first();
        $transfer_products = ProductTransfer::with( 'detail', 'product' )->where( 'transfer_id', '=', $id )->get();
        return view( 'admin.neeting.show', compact( 'transfer', 'transfer_products' ) );

    }

    /**
     * @param $id
     */
    public function expenseList( $id )
    {
        // Here id is Transfer ID
        $expenses = Expense::with( 'expense_category', 'material' )->where( 'transfer_id', $id )->get();
        return view( 'admin.neeting.expense.show', compact( 'expenses' ) );

    }

    public function expenses()
    {
        $expenses = Expense::where( 'department_id', 1 )->get();

        return view( 'admin.expenses.index', compact( 'expenses' ) );
    }

    public function stockIn()
    {
        $materials = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        // $colors = MaterialConfig::where('type',3)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $companies = Company::pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ) );
        return view( 'admin.neeting.stockIn', compact( 'materials', 'companies' ) );

    }

    public function stockOut()
    {
        $materials = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        // $colors = MaterialConfig::where('type',3)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $companies = Company::pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ) );
        return view( 'admin.neeting.stock-out', compact( 'materials', 'companies' ) );

    }

    /**
     * @param Request $request
     */
    public function search( Request $request )
    {

        $product_id         = $request->product_id;
        $quantity           = $request->quantity;
        $company_id         = $request->company_id;
        $process_fee        = $request->process_fee;
        $materials          = MaterialConfig::where( 'type', 1 )->get();
        $check_company_type = Company::where( 'id', $company_id )->first();

        if ( $check_company_type->type == 1 ) {
            $check_quantity = $this->checkProductQuantity( $request );
            if ( !$check_quantity ) {
                $material_name = MaterialConfig::find( $product_id );
                return ['status' => 103, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
            }
        }
        $storeProduct = $this->store( $request );

        if ( $storeProduct ) {
            logger( 'Store ' );
        }
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function store( Request $request )
    {
        DB::beginTransaction();
        try {
            $product_id  = $request->product_id;
            $quantity    = $request->quantity;
            $company_id  = $request->company_id;
            $process_fee = $request->process_fee;

            logger( 'Product Id ' . $product_id . ' quantity ' . $quantity );

            $check_company_type = Company::where( 'id', $company_id )->first();

            $transfer                      = new Transfer();
            $transferData['company_id']    = $company_id;
            $transferData['department_id'] = 1;
            $transferData['created_by']    = Auth::user()->id;
            $transferData['date']          = date( "Y-m-d" );
            $storeTransfer                 = $transfer->create( $transferData );

            $transfer_id = $storeTransfer->id;
            if ( $check_company_type->type == 1 ) {
                $getAllStock = MaterialIn::where( 'material_id', $product_id )->where( 'rest', '>', 0 )->get();
                $contentQty  = $quantity;
                foreach ( $getAllStock as $stock ) {
                    $pro_qty = $contentQty;
                    if ( $stock->rest < $contentQty ) {
                        $pro_qty    = $stock->rest;
                        $contentQty = $contentQty - $stock->rest;

                        $productTransfer                     = new ProductTransfer();
                        $transferProduct['product_id']       = $product_id;
                        $transferProduct['quantity']         = $pro_qty;
                        $transferProduct['rest_quantity']    = $pro_qty;
                        $transferProduct['transfer_id']      = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['process_fee']      = $process_fee;
                        $transferProduct['created_by']       = Auth::user()->id;

                        logger( 'Product id store 1' . $transferProduct );

                        $storeTransfer = $productTransfer->create( $transferProduct );

                        if ( $storeTransfer ) {
                            // reduce stock quantity
                            $data['rest'] = $stock->rest - $pro_qty;
                            DB::table( 'material_ins' )->where( 'id', $stock->id )->update( $data );

                            // Add expense

                            $expense                      = new Expense();
                            $expense->entry_date          = date( "Y-m-d" );
                            $expense->amount              = ( $pro_qty * $stock->unit_price );
                            $expense->description         = "Product Purchase Costing for Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id       = 1;
                            $expense->created_by_id       = Auth::user()->id;
                            $expense->material_id         = $stock->material_id;
                            $expense->transfer_id         = $transfer_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                        }
                    } else {
                        $contentQty                          = 0;
                        $productTransfer                     = new ProductTransfer();
                        $transferProduct['product_id']       = $product_id;
                        $transferProduct['quantity']         = $pro_qty;
                        $transferProduct['rest_quantity']    = $pro_qty;
                        $transferProduct['transfer_id']      = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['process_fee']      = $process_fee;
                        $transferProduct['created_by']       = Auth::user()->id;

                        $storeTransfer = $productTransfer->create( $transferProduct );
                        if ( $storeTransfer ) {
                            // reduce stock quantity
                            $data['rest'] = $stock->rest - $pro_qty;
                            DB::table( 'material_ins' )->where( 'id', $stock->id )->update( $data );

                            // Add expense

                            $expense                      = new Expense();
                            $expense->entry_date          = date( "Y-m-d" );
                            $expense->amount              = ( $pro_qty * $stock->unit_price );
                            $expense->description         = "Product Purchase Costing for Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id       = 1;
                            $expense->created_by_id       = Auth::user()->id;
                            $expense->material_id         = $stock->material_id;
                            $expense->transfer_id         = $transfer_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                        }
                    }

                    if ( $contentQty < 1 ) {
                        break;
                    }

                }
            } else {
                $productTransfer                        = new ProductTransfer();
                $transferProduct['product_id']          = $product_id;
                $transferProduct['quantity']            = $quantity;
                $transferProduct['rest_quantity']       = $quantity;
                $transferProduct['transfer_id']         = $transfer_id;
                $transferProduct['product_stock_id']    = null;
                $transferProduct['process_fee']         = $process_fee;
                $transferProduct['process_unit_charge'] = 0;
                $transferProduct['created_by']          = Auth::user()->id;

                $storeTransfer = $productTransfer->create( $transferProduct );
            }
            DB::commit();
            logger( ' Commit Done' );
            return ['status' => 200, 'message' => 'Successfully Transfer'];

        } catch ( \Exception $e ) {
            DB::rollback();
            return $e;
        }
    }

    /**
     * @param $request
     */
    private function checkProductQuantity( $request )
    {
        $total_stock_qty = MaterialIn::where( 'material_id', $request->product_id )->sum( 'rest' );
        if ( $total_stock_qty > $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $request
     */
    private function checkMaterialQuantity( $request )
    {
        $total_stock_qty = MaterialIn::where( 'material_id', $request['product_id'] )->sum( 'rest' );
        if ( $total_stock_qty > $request['quantity'] ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $request
     */
    private function checkKneetingProductQuantity( $request )
    {
        $company         = $request->company_id;
        $product_id      = $request->product_id;
        $total_stock_qty = ProductTransfer::where( 'product_id', $product_id )->with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company ) {
            $query->where( 'company_id', '=', $company )->where( 'department_id', 1 );
        } )->sum( 'rest_quantity' );
        if ( $total_stock_qty >= $request->quantity ) {
            return true;
        } else {
            return false;
        }
    }
}
