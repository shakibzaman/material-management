<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Expense;
use App\ExpenseCategory;
use App\Product;
use App\ProductCosting;
use App\StockSet;
use App\Transfer;
use App\Department;
use App\MaterialIn;
use App\MaterialConfig;
use App\ProductTransfer;
use App\MaterialTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DyeingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nettingsData      = Transfer::with( 'company' )->where( 'department_id', 2 )->get()->groupBy( 'company_id' );
        $transfer_products = ProductTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) {
            $query->where( 'department_id', '=', 2 );
        } )->get()->groupBy( 'transfer.company_id' );

        $transfer_materials = MaterialTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) {
            $query->where( 'department_id', '=', 2 );
        } )->get()->groupBy( 'transfer.company_id' );

        $companyList = Company::get()->keyBy( 'id' );
        return view( 'admin.dyeing.index', compact( 'nettingsData', 'transfer_products', 'transfer_materials', 'companyList' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function stockIn()
    {
        $materials = MaterialConfig::where( 'type', 2 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $colors    = MaterialConfig::where( 'type', 3 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $companies = Company::pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ) );
        return view( 'admin.neeting.stockIn', compact( 'materials', 'colors', 'companies' ) );

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

    /**
     * @param $id
     */
    public function transferList( $id )
    {
        // id is for Company ID
        $nettingsData       = Transfer::with( 'company' )->where( 'department_id', 2 )->where( 'company_id', '=', $id )->get();
        $transfer_products  = ProductTransfer::with( 'transfer', 'product' )->get()->groupBy( 'transfer_id' );
        $transfer_materials = MaterialTransfer::with( 'transfer' )->get()->groupBy( 'transfer_id' );
        return view( 'admin.dyeing.company.transfer-list', compact( 'nettingsData', 'transfer_products', 'transfer_materials' ) );

    }

    /**
     * @param  $id
     * @return mixed
     */
    public function transferShow( $id )
    {
        $transfer           = Transfer::with( 'company' )->where( 'id', $id )->first();
        $transfer_products  = ProductTransfer::with( 'product_transfer_detail', 'product' )->where( 'transfer_id', '=', $id )->get();
        $transfer_materials = MaterialTransfer::with( 'detail', 'material' )->where( 'transfer_id', '=', $id )->get();
        return view( 'admin.dyeing.show', compact( 'transfer', 'transfer_products', 'transfer_materials' ) );

    }

    /**
     * @param $id
     */
    public function transferProductShowroom( $id )
    {
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 2 );
        } )->get()->groupBy( 'product_id' );

        $materials = ProductTransfer::with( 'transfer', 'product' )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id );
        } )->get()->pluck( 'product.name', 'product.id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $colors          = MaterialConfig::where( 'type', 3 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $company_id      = $id;
        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        $showrooms       = Department::whereNotIn( 'id', [1, 2] )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        return view( 'admin.showroom.stock-transfer', compact( 'showrooms', 'rest_quantity', 'materials', 'colors', 'company_id', 'material_key_by' ) );
    }

    /**
     * @param $id
     */
    public function transferProduct( $id )
    {
        $rest_quantity = ProductTransfer::with( 'transfer' )->where( 'rest_quantity', '>', 0 )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id )->where( 'department_id', 2 );
        } )->get()->groupBy( 'product_id' );
        $materials = ProductTransfer::with( 'transfer', 'product' )->whereHas( 'transfer', function ( Builder $query ) use ( $id ) {
            $query->where( 'company_id', '=', $id );
        } )->get()->pluck( 'product.name', 'product.id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $colors          = MaterialConfig::where( 'type', 3 )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        $company_id      = $id;
        $material_key_by = MaterialConfig::get()->keyBy( 'id' );
        $showrooms       = Department::whereNotIn( 'id', [1, 2] )->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );
        return view( 'admin.dyeing.stock-transfer', compact( 'showrooms', 'rest_quantity', 'materials', 'colors', 'company_id', 'material_key_by' ) );
    }

    public function expenses()
    {
        $expense_categories = ExpenseCategory::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $expenses = Expense::where( 'department_id', 2 )->get();

        return view( 'admin.expenses.index', compact( 'expenses' ,'expense_categories') );
    }

    /**
     * @param Request $request
     */
    public function search( Request $request )
    {
        $quantity    = $request->quantity;
        $company_id  = $request->company_id;
        $materials   = MaterialConfig::where( 'type', 1 )->get();
        $color_id    = $request->color_id;
        $process_loss = $request->process_loss;
        $showroom_id = $request->showroom_id;

        $check_quantity = $this->checkKneetingProductQuantity( $request );
        if ( !$check_quantity ) {
            return ['status' => 103, 'message' => "Sorry !!! Low Stock"];
        }
        $sets = StockSet::where( 'start_quantity', '<=', $quantity )->where( 'end_quantity', '>=', $quantity )
                                                                  ->where( 'color_id', $color_id )->first();
        if ( !$sets ) {
            $sets = [];
        }
        return view( 'admin.dyeing.include.stock-list', compact( 'showroom_id', 'sets', 'materials', 'quantity', 'company_id', 'color_id', 'process_loss' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $product_id  = env("FABRICS_ID",65);
            $quantity    = $request->quantity;
            $company_id  = $request->company_id;
            $color_id    = $request->color_id;
            $dyeing_charge = $request->dyeing_charge;
            $dry_charge = $request->dry_charge;
            $compacting_charge = $request->compacting_charge;
            $process_loss = !is_null($request->process_loss) ? $request->process_loss : 0;
            $processed = $quantity - $request->process_loss;

            $check_company_type = Company::where( 'id', $company_id )->first();

            $transfer                      = new Transfer();
            $transferData['company_id']    = $company_id;
            $transferData['department_id'] = $request->showroom_id;
            $transferData['created_by']    = Auth::user()->id;
            $transferData['date']          = date( "Y-m-d" );
            $storeTransfer                 = $transfer->create( $transferData );
            $transfer_id = $storeTransfer->id;
            if ( $check_company_type->type == 1 ) {
                $company = $request->company_id;
                $product = $request->product_id;

                $getAllStock = ProductTransfer::with( 'transfer' )
                    ->where( 'rest_quantity', '>', 0 )
                    ->whereHas( 'transfer', function ( Builder $query ) use ( $company, $product ) {
                        $query->where( 'company_id', '=', $company )->where( 'department_id', 2 );
                    } )->get();

                $process_fee_calculation = $dyeing_charge+$dry_charge+$compacting_charge;

                // Processed Product start
                if($processed>0){
                    $contentQty = $processed;
                    foreach ( $getAllStock as $stock ) {

                        $pro_qty = $contentQty;
                        if ($stock->rest_quantity < $contentQty) {
                            $pro_qty = $stock->rest_quantity;
                            $contentQty = $contentQty - $stock->rest_quantity;

                            $productTransfer = new ProductTransfer();
                            $productTransfer->product_id = $product_id;
                            $productTransfer->quantity = $pro_qty;
                            $productTransfer->rest_quantity = $pro_qty;
                            $productTransfer->transfer_id = $transfer_id;
                            $productTransfer->product_stock_id = $stock->id;
                            $productTransfer->color_id = $color_id;
                            $productTransfer->dyeing_charge = $dyeing_charge;
                            $productTransfer->dry_charge = $dry_charge;
                            $productTransfer->compacting_charge = $compacting_charge;
                            $productTransfer->process_fee = $process_fee_calculation;
                            $productTransfer->created_by = Auth::user()->id;
                            $productTransfer->process_type = 1;
                            $productTransferStore = $productTransfer->save();

                            $product_transfer_id = $productTransfer->id;

                           $material_used = $this->_materialUse($request, $transfer_id, $product_transfer_id = $productTransfer->id, $product_qty = $pro_qty);
                            if ($material_used['status'] == 200) {
                                logger('Material  Used');
                            }
                            if ($material_used['status'] != 200) {
                                logger(' material Not used');
                                return ['status'=>104,'message'=>'Material not accessed,Try again'];
                            }
                            // Add Product
                            $material_transfer = MaterialTransfer::where('product_transfer_id', $product_transfer_id)
                                ->where('transfer_id',$transfer_id)->get();
                            $material_costing = 0;
                            if (count($material_transfer) > 0) {
                                foreach ($material_transfer as $material) {
                                    $materialIn = MaterialIn::where('id', $material->material_stock_id)->first();
                                    $material_line_total = $material->quantity * $materialIn->unit_price;
                                    $material_costing += $material_line_total;
                                }
                            }

                            $releted_knitting_stock = ProductTransfer::where('id', $stock->product_stock_id)->first();
                            $material_details = MaterialIn::where('id', $releted_knitting_stock->product_stock_id)->first();
                            $product_costing = $material_details->unit_price;
                            $knitting_charge = $releted_knitting_stock->process_fee;
                            $product_final_costing = $product_costing + $knitting_charge + $process_fee_calculation + ($material_costing/$pro_qty) ;

                            $product = new Product();
                            $product->color_id = $color_id;
                            $product->product_transfer_id = $productTransfer->id;
                            $product->process_costing = $product_final_costing;
                            $product->showroom_id = $request->showroom_id;
                            $product->quantity = $pro_qty;
                            $product->type = 1; // 1 = Process Product
                            $product->save();

                            $product_costing_history = new ProductCosting();
                            $product_costing_history->product_row_id = $product->id;
                            $product_costing_history->product_costing = $product_costing;
                            $product_costing_history->knitting_charge = $knitting_charge;
                            $product_costing_history->dyeing_charge = $dyeing_charge;
                            $product_costing_history->dry_charge = $dry_charge;
                            $product_costing_history->compacting_charge = $compacting_charge;
                            $product_costing_history->material_costing = $material_costing;
                            $product_costing_history->created_by = Auth::user()->id;
                            $product_costing_history->save();
                            logger("Product Costing History");

                            if ($productTransferStore) {
                                // reduce stock quantity
                                $data_processed['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                                logger('Processed qty is '.$pro_qty);
                                $processed_data_update = DB::table('product_transfer')->where('id', $stock->id)->update($data_processed);
                                if($processed_data_update){
                                    logger(' Updated rest qty 2 for processed' . $data_processed['rest_quantity']);
                                }

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($product_costing * $pro_qty);
                                $expense->description = "Product Process Costing of Dyeing";
                                $expense->expense_category_id = 1;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->product_id;
                                $expense->transfer_product_id = $productTransfer->id;
                                $expense->save();
                                logger('Expense 1' . $expense);
                                logger('Product Process Costing of Dyeing');


                            }

                        } else {
                            $contentQty = 0;
                            $productTransfer = new ProductTransfer();
                            $productTransfer->product_id = $product_id;
                            $productTransfer->quantity = $pro_qty;
                            $productTransfer->rest_quantity = $pro_qty;
                            $productTransfer->transfer_id = $transfer_id;
                            $productTransfer->product_stock_id = $stock->id;
                            $productTransfer->color_id = $color_id;
                            $productTransfer->dyeing_charge = $dyeing_charge;
                            $productTransfer->dry_charge = $dry_charge;
                            $productTransfer->compacting_charge = $compacting_charge;
                            $productTransfer->process_fee = $process_fee_calculation;
                            $productTransfer->created_by = Auth::user()->id;
                            $productTransfer->process_type = 1;
                            $productTransferStore = $productTransfer->save();


                            $product_transfer_id = $productTransfer->id;

                            $material_used = $this->_materialUse($request, $transfer_id, $product_transfer_id = $productTransfer->id, $product_qty = $pro_qty);
                            if ($material_used['status'] == 200) {
                                logger('Material  Used');
                            }
                            if ($material_used['status'] != 200) {
                                logger(' material Not used');
                                return ['status'=>104,'message'=>'Material not accessed,Try again'];
                            }
                            // Add Product
                            $material_transfer = MaterialTransfer::where('product_transfer_id', $product_transfer_id)->get();
                            $material_costing = 0;
                            if (count($material_transfer) > 0) {
                                foreach ($material_transfer as $material) {
                                    $materialIn = MaterialIn::where('id', $material->material_stock_id)->first();
                                    $material_line_total = $material->quantity * $materialIn->unit_price;
                                    $material_costing += $material_line_total;
                                }
                            }

                            $releted_knitting_stock = ProductTransfer::where('id', $stock->product_stock_id)->first();
                            $material_details = MaterialIn::where('id', $releted_knitting_stock->product_stock_id)->first();
                            $product_costing = $material_details->unit_price;
                            $knitting_charge = $releted_knitting_stock->process_fee;
                            $product_final_costing = $product_costing + $knitting_charge + $process_fee_calculation + ($material_costing/$pro_qty) ;

                            $product = new Product();
                            $product->color_id = $color_id;
                            $product->product_transfer_id = $productTransfer->id;
                            $product->showroom_id = $request->showroom_id;
                            $product->process_costing = $product_final_costing;
                            $product->quantity = $pro_qty;
                            $product->type = 1; // 1 = Process Product
                            $product->save();

                            $product_costing_history = new ProductCosting();
                            $product_costing_history->product_row_id = $product->id;
                            $product_costing_history->product_costing = $product_costing;
                            $product_costing_history->knitting_charge = $knitting_charge;
                            $product_costing_history->dyeing_charge = $dyeing_charge;
                            $product_costing_history->dry_charge = $dry_charge;
                            $product_costing_history->compacting_charge = $compacting_charge;
                            $product_costing_history->material_costing = $material_costing;
                            $product_costing_history->created_by = Auth::user()->id;
                            $product_costing_history->save();
                            logger("Product Costing History");

                            if ($productTransferStore) {
                                // reduce stock quantity
                                $data_processed['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                                logger('Processed qty is 2 '.$pro_qty);
                                $processed_data_update = DB::table('product_transfer')->where('id', $stock->id)->update($data_processed);
                                if($processed_data_update){
                                    logger(' Updated rest qty 2 for processed' . $data_processed['rest_quantity']);
                                }

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = $product_costing * $pro_qty;
                                $expense->description = "Product Process Costing of Dyeing";
                                $expense->expense_category_id = 1;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->product_id;
                                $expense->transfer_product_id = $productTransfer->id;
                                $expense->save();
                                logger('Expense 2' . $expense);
                                logger('Product Process Costing of Dyeing');
                            }
                        }

                        if ( $contentQty < 1 ) {
                            break;
                        }

                    }
                }

                // Process Loss start
                if($process_loss>0){
                    $contentQty = $process_loss;
                    foreach ( $getAllStock as $stock ) {

                        $pro_qty = $contentQty;
                        if ($stock->rest_quantity < $contentQty) {
                            $pro_qty = $stock->rest_quantity;
                            $contentQty = $contentQty - $stock->rest_quantity;

                            $productTransfer = new ProductTransfer();
                            $productTransfer->product_id = $product_id;
                            $productTransfer->quantity = $pro_qty;
                            $productTransfer->rest_quantity = $pro_qty;
                            $productTransfer->transfer_id = $transfer_id;
                            $productTransfer->product_stock_id = $stock->id;
                            $productTransfer->color_id = $color_id;
                            $productTransfer->dyeing_charge = $dyeing_charge;
                            $productTransfer->dry_charge = $dry_charge;
                            $productTransfer->compacting_charge = $compacting_charge;
                            $productTransfer->process_fee = $process_fee_calculation;
                            $productTransfer->created_by = Auth::user()->id;
                            $productTransfer->process_type = 2;
                            $productTransferStore = $productTransfer->save();

                            $product_transfer_id = $productTransfer->id;

                            $material_used = $this->_materialUse($request, $transfer_id, $product_transfer_id = $productTransfer->id, $product_qty = $pro_qty);
                            if ($material_used['status'] == 200) {
                                logger('Material  Used');
                            }
                            if ($material_used['status'] != 200) {
                                logger(' material Not used');
                                return ['status'=>104,'message'=>'Material not accessed,Try again'];
                            }
                            // Add Product
                            $material_transfer = MaterialTransfer::where('product_transfer_id', $product_transfer_id)
                                ->where('transfer_id',$transfer_id)->get();
                            $material_costing = 0;
                            if (count($material_transfer) > 0) {
                                foreach ($material_transfer as $material) {
                                    $materialIn = MaterialIn::where('id', $material->material_stock_id)->first();
                                    $material_line_total = $material->quantity * $materialIn->unit_price;
                                    $material_costing += $material_line_total;
                                }
                            }

                            $releted_knitting_stock = ProductTransfer::where('id', $stock->product_stock_id)->first();
                            $material_details = MaterialIn::where('id', $releted_knitting_stock->product_stock_id)->first();
                            $product_costing = $material_details->unit_price;
                            $knitting_charge = $releted_knitting_stock->process_fee;
                            $product_final_costing = $product_costing + $knitting_charge + $process_fee_calculation + ($material_costing/$pro_qty) ;

                            if ($productTransferStore) {
                                // reduce stock quantity

                                $rest_stock = DB::table('product_transfer')->where('id', $stock->id)->first();
                                $data['rest_quantity'] = $rest_stock->rest_quantity - $pro_qty;
                                logger('Processed loss qty is 2 '.$pro_qty);

                                DB::table('product_transfer')->where('id', $stock->id)->update($data);
                                logger(' Updated rest qty 1 for processe loss' . $data['rest_quantity']);



                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($product_costing * $pro_qty);
                                $expense->description = "Product Process Costing of Dyeing";
                                $expense->expense_category_id = 1;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->product_id;
                                $expense->transfer_product_id = $productTransfer->id;
                                $expense->save();
                                logger('Expense 1' . $expense);
                                logger('Product Process Costing of Dyeing');


                            }

                        } else {
                            $contentQty = 0;
                            $productTransfer = new ProductTransfer();
                            $productTransfer->product_id = $product_id;
                            $productTransfer->quantity = $pro_qty;
                            $productTransfer->rest_quantity = $pro_qty;
                            $productTransfer->transfer_id = $transfer_id;
                            $productTransfer->product_stock_id = $stock->id;
                            $productTransfer->color_id = $color_id;
                            $productTransfer->dyeing_charge = $dyeing_charge;
                            $productTransfer->dry_charge = $dry_charge;
                            $productTransfer->compacting_charge = $compacting_charge;
                            $productTransfer->process_fee = $process_fee_calculation;
                            $productTransfer->created_by = Auth::user()->id;
                            $productTransfer->process_type = 2;
                            $productTransferStore = $productTransfer->save();


                            $product_transfer_id = $productTransfer->id;

                            $material_used = $this->_materialUse($request, $transfer_id, $product_transfer_id = $productTransfer->id, $product_qty = $pro_qty);
                            if ($material_used['status'] == 200) {
                                logger('Material  Used');
                            }
                            if ($material_used['status'] != 200) {
                                logger(' material Not used');
                                return ['status'=>104,'message'=>'Material not accessed,Try again'];
                            }
                            // Add Product
                            $material_transfer = MaterialTransfer::where('product_transfer_id', $product_transfer_id)
                                ->where('transfer_id',$transfer_id)->get();
                            $material_costing = 0;
                            if (count($material_transfer) > 0) {
                                foreach ($material_transfer as $material) {
                                    $materialIn = MaterialIn::where('id', $material->material_stock_id)->first();
                                    $material_line_total = $material->quantity * $materialIn->unit_price;
                                    $material_costing += $material_line_total;
                                }
                            }

                            $releted_knitting_stock = ProductTransfer::where('id', $stock->product_stock_id)->first();
                            $material_details = MaterialIn::where('id', $releted_knitting_stock->product_stock_id)->first();
                            $product_costing = $material_details->unit_price;
                            $knitting_charge = $releted_knitting_stock->process_fee;
                            $product_final_costing = $product_costing + $knitting_charge + $process_fee_calculation + ($material_costing/$pro_qty) ;

                            if ($productTransferStore) {
                                // reduce stock quantity
                                $rest_stock = DB::table('product_transfer')->where('id', $stock->id)->first();
                                $data['rest_quantity'] = $rest_stock->rest_quantity - $pro_qty;
                                logger('Processed loss qty is 2 '.$pro_qty);

                                DB::table('product_transfer')->where('id', $stock->id)->update($data);
                                logger(' Updated rest qty 2 for processe loss' . $data['rest_quantity']);


                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = $product_costing * $pro_qty;
                                $expense->description = "Product Process Costing of Dyeing";
                                $expense->expense_category_id = 1;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->product_id;
                                $expense->transfer_product_id = $productTransfer->id;
                                $expense->save();
                                logger('Expense 2' . $expense);
                                logger('Product Process Costing of Dyeing');
                            }
                        }

                        if ( $contentQty < 1 ) {
                            break;
                        }

                    }
                }
            }
            DB::commit();
            return ['status' => 200, 'message' => 'Successfully Transfer to showroom'];
        } catch ( \Exception $e ) {
            DB::rollback();
            return $e->getMessage();
        }
    }


    private function _materialUse($request,$transfer_id,$product_transfer_id,$product_qty){
        DB::beginTransaction();
        try {
            foreach ($request->material_qty as $key => $material) {
                if ($material != null) {
                    $materialRequest['product_id'] = $key;
                    $materialRequest['quantity'] = $material;
                    $material_name = MaterialConfig::find($key);
                    $checkMaterialQty = $this->checkMaterialQuantity($materialRequest);
                    if (!$checkMaterialQty) {
                        DB::rollback();
                        return ['status' => 104, 'message' => "Sorry !!!  " . $material_name->name . " Low Stock"];
                    }
                    logger("Before total_material_stocks");
                   $total_material_stocks = MaterialIn::where('material_id', $key)->where('rest', '>', 0)->get();

                   $needed_material_for_product = ($product_qty * $material) / $request->quantity;
                    $contentQty = $needed_material_for_product;
                    foreach ($total_material_stocks as $stock) {
                        logger("Foreach in total_material_stocks");

                        $pro_qty = $contentQty;
                        if ($stock->rest < $contentQty) {
                            $pro_qty = $stock->rest;
                            $contentQty = $contentQty - $stock->rest;

                            $materialStore = new MaterialTransfer();
                            $materialStore->material_id = $key;
                            $materialStore->transfer_id = $transfer_id;
                            $materialStore->quantity = round($pro_qty,2);
                            $materialStore->material_stock_id = $stock->id;
                            $materialStore->product_transfer_id = $product_transfer_id;
                            $materialStore->created_by = Auth::user()->id;
                            $materialDataStore = $materialStore->save();
                            logger("Material Pro qty is ".$pro_qty);

                            if ($materialDataStore) {
                                // reduce stock quantity
                                $material_data['rest'] = $stock->rest - $pro_qty;
                                DB::table('material_ins')->where('id', $stock->id)->update($material_data);

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($pro_qty * $stock->unit_price);
                                $expense->description = "Material Costing of Dyeing for Qty ".$pro_qty . " Price is ".($pro_qty * $stock->unit_price);
                                $expense->expense_category_id = 2;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->material_id;
                                $expense->transfer_product_id = $product_transfer_id;
                                $expense->save();
                                logger('Expense Stored 300' . $expense);

                            }
                        } else {

                            $contentQty = 0;
                            $materialStore = new MaterialTransfer();
                            $materialStore->material_id = $key;
                            $materialStore->transfer_id = $transfer_id;
                            $materialStore->quantity = round($pro_qty,2);
                            $materialStore->material_stock_id = $stock->id;
                            $materialStore->product_transfer_id = $product_transfer_id;
                            $materialStore->created_by = Auth::user()->id;
                            $materialDataStore = $materialStore->save();
                            logger("Material Pro qty is ".$pro_qty);

                            if ($materialDataStore) {

                                // reduce stock quantity
                                $material_data['rest'] = $stock->rest - $pro_qty;
                                DB::table('material_ins')->where('id', $stock->id)->update($material_data);

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($pro_qty * $stock->unit_price);
                                $expense->description = "Material Costing of Dyeing for Qty ".$pro_qty . " Price is ".($pro_qty * $stock->unit_price);
                                $expense->expense_category_id = 2;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->material_id;
                                $expense->transfer_product_id = $product_transfer_id;
                                $expense->save();

                                logger('Expense Store 400' . $expense);

                            }
                        }
                        if ($contentQty < 1) {
                            break;
                        }
                    }

                }
            }
            DB::commit();
            logger("Material Used Commit done");
            return ['status'=>200,'message'=>['Material Used Commit done']];
        }catch ( \Exception $e ) {
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
        //
    }

    public function transferProductDetails($department_id,$product_id,$color_id){
        $transfer_material_detail = DB::table('material_transfer')
        ->join('transfer','material_transfer.transfer_id','transfer.id')
        ->join('product_transfer','material_transfer.transfer_id','product_transfer.transfer_id')
        ->join('material_configs','material_transfer.material_id','material_configs.id')
        ->join('material_configs AS product_name','product_transfer.product_id','product_name.id')
        ->where('transfer.department_id',$department_id)
        ->where('product_transfer.color_id',$color_id)
        ->where('product_transfer.product_id',$product_id)
        ->select('material_configs.name','material_transfer.*','product_transfer.quantity AS product_quantity','product_name.name as product_name')
        ->get()->groupBy('transfer_id');
        // $transfer_materials = MaterialTransfer::with('transfer','material')
        // ->whereHas('transfer', function (Builder $query) use ($department_id){
        //     $query->where('department_id', $department_id);
        // })->get()->groupBy('transfer.department_id');
        // return [$company_id,$product_id,$color_id];
        return view('admin.dyeing.material-details',compact('transfer_material_detail'));
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

    /**
     * @param $request
     */
    private function checkKneetingProductQuantity( $request )
    {
        $company         = $request->company_id;
        $total_stock_qty = ProductTransfer::with( 'transfer' )->whereHas( 'transfer', function ( Builder $query ) use ( $company ) {
            $query->where( 'company_id', '=', $company )->where( 'department_id', 2 );
        } )->sum( 'rest_quantity' );
        if ( $total_stock_qty >= $request->quantity ) {
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
}
