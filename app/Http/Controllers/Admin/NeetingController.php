<?php

namespace App\Http\Controllers\admin;

use Gate;
use App\User;
use App\Income;
use App\Company;
use App\Expense;
use App\Payment;
use App\Customer;
use App\Transfer;
use App\MaterialIn;
use App\UserAccount;
use App\ProductReturn;
use App\MaterialConfig;
use App\ProductTransfer;
use App\MaterialTransfer;
use App\ProductDelivered;
use Illuminate\Http\Request;
use App\ProductDeliveredDetail;
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
     * @param $id
     */
    public function stockDelivered( $id )
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
        return view( 'admin.neeting.modal.stock-delivered', compact( 'product_list', 'material_key_by', 'knittingStock', 'company_id', 'type', 'rest_quantity' ) );

    }

    /**
     * @param Request $request
     */
    public function stockDeliveredCheck( Request $request )
    {
        $company_id = $request->company_id;

        $getAllStock = ProductTransfer::with( 'transfer' )
            ->where( 'product_id', $request->product_id )
            ->where( 'rest_quantity', '>', 0 )
            ->whereHas( 'transfer', function ( Builder $query ) use ( $company_id ) {
                $query->where( 'company_id', '=', $company_id )->where( 'department_id', 1 );
            } )->get();
        $total_quantity = $getAllStock->sum( 'rest_quantity' );
        if ( $total_quantity < $request->transfer_stock ) {
            return ['status' => 103, 'message' => 'Sorry you can\'t Delivered more then you have'];
        }
        $stockDetails = [];
        $contentQty   = $request->transfer_stock;
        foreach ( $getAllStock as $stock ) {
            $pro_qty = $contentQty;
            if ( $stock->rest_quantity < $contentQty ) {
                $pro_qty    = $stock->rest_quantity;
                $contentQty = $contentQty - $stock->rest_quantity;

                // Product Transfer update start
                $stockDetails[$stock->id] = $pro_qty;

            } else {
                $contentQty               = 0;
                $stockDetails[$stock->id] = $pro_qty;

            }
            if ( $contentQty < 1 ) {
                break;
            }
        }

        $stocks = ProductTransfer::whereIn( 'id', array_keys( $stockDetails ) )->get()->keyBy( 'id' );
        return view( 'admin.neeting.include.delivered-bill', compact( 'stockDetails', 'stocks' ) );

    }

    /**
     * @param $id
     */
    public function stockDeliveredList( $id )
    {
        $delivered_list = ProductDelivered::with( 'product' )->where( 'company_id', $id )->get();
        return view( 'admin.neeting.modal.stock-delivered-list', compact( 'delivered_list' ) );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function stockDeliveredToCompany( Request $request )
    {

        DB::beginTransaction();
        try {
            // Bill Generate
            $delivered              = new ProductDelivered();
            $delivered->product_id  = $request->product_id;
            $delivered->quantity    = $request->transfer_stock;
            $delivered->company_id  = $request->company_id;
            $delivered->process_fee = $request->total_process_fee;
            $delivered->bill_fee    = $request->bill_total;
            $delivered->sub_total   = $request->bill_total;
            $delivered->total       = ( $request->bill_total - $request->discount );
            $delivered->paid        = $request->paid;
            $delivered->due         = $request->due;
            $delivered->discount    = $request->discount;
            $delivered->date        = $request->date;
            $delivered->created_by  = Auth::user()->id;
            $delivered->save();

            logger( " Bill Generate " );

            // Adjust Company due amount
            // User Account update start
            if ( $request->due > 0 ) {
                $users_account           = UserAccount::where( 'user_id', $request->company_id )->where( 'type', 3 )->first();
                $update_due['total_due'] = $users_account->total_due + $request->due;
                $users_account->update( $update_due );

                logger( " Update user account due" );
            }
            // User Account update end

            // Income generate start
            $income                     = new Income();
            $income->entry_date         = $request->date;
            $income->amount             = $request->bill_total - $request->total_process_fee;
            $income->description        = 'Income from Knitting product delivered';
            $income->created_by_id      = Auth::user()->id;
            $income->income_category_id = 1; // 1 is for Knitting
            $income->releted_id         = $delivered->id;
            $income->releted_id_type    = 3;
            $income->save();
            // Income generate End

            // Delivered details Info start
            // Deduct Quantity from Stock start
            for ( $i = 0; $i < count( $request->stock_id ); $i++ ) {
                logger( "Deduction start" );
                $stock = ProductTransfer::where( 'id', $request->stock_id[$i] )->first();

                $data['rest_quantity'] = $stock->rest_quantity - $request->stock_value[$i];
                DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );

                logger( "Deduct Quantity from Stock" );

                $deliveryDetails                       = new ProductDeliveredDetail();
                $deliveryDetails->product_delivered_id = $delivered->id;
                $deliveryDetails->product_id           = $stock->product_id;
                $deliveryDetails->product_stock_id     = $stock->id;
                $deliveryDetails->process_fee          = $stock->process_fee;
                $deliveryDetails->quantity             = $request->stock_value[$i];
                $deliveryDetails->save();

                logger( "Delivered details Info" );

            }
            if ( $request->paid ) {
                // Payment data store start
                $payment                  = new Payment();
                $payment->amount          = $request->paid;
                $payment->payment_process = $request->payment_process;
                $payment->payment_info    = $request->payment_info;
                $payment->user_account_id = $request->company_id;
                $payment->releted_id      = $delivered->id;
                $payment->releted_id_type = 3;
                $payment->created_by      = Auth::user()->id;
                $payment->save();
                // Payment data store end
            }

            DB::commit();
            return ['status' => 200, 'message' => 'Delivered Successfully'];
        } catch ( \Exception $e ) {
            DB::rollBack();
            return $e->getMessage();
        }

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
