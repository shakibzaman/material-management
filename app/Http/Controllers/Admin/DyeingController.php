<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Expense;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\MaterialTransfer;
use App\ProductTransfer;
use App\Transfer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DyeingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nettingsData = Transfer::with('company')->where('department_id',2)->get()->groupBy('company_id');
        $transfer_products = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query){
            $query->where('department_id', '=', 2);
        })->get()->groupBy('transfer.company_id');

        $transfer_materials = MaterialTransfer::with('transfer')->whereHas('transfer', function (Builder $query){
            $query->where('department_id', '=', 2);
        })->get()->groupBy('transfer.company_id');

        $companyList = Company::get()->keyBy('id');
        return view('admin.dyeing.index',compact('nettingsData','transfer_products','transfer_materials','companyList'));
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
        $materials = MaterialConfig::where('type',2)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $colors = MaterialConfig::where('type',3)->pluck('name','id')->prepend(trans('global.pleaseSelect'),'');
        $companies = Company::pluck('name','id')->prepend(trans('global.pleaseSelect'));
        return view('admin.neeting.stockIn',compact('materials','colors','companies'));

    }
    public function expenseList($id){
        // Here id is Transfer ID
        $expenses = Expense::with('expense_category','material')->where('transfer_id',$id)->get();
        return view('admin.neeting.expense.show',compact('expenses'));

    }

    public function transferList($id)
    {
        // id is for Company ID
        $nettingsData = Transfer::with('company')->where('department_id',2)->where('company_id','=',$id)->get();
        $transfer_products = ProductTransfer::with('transfer','product')->get()->groupBy('transfer_id');
        $transfer_materials = MaterialTransfer::with('transfer')->get()->groupBy('transfer_id');
        return view('admin.dyeing.company.transfer-list',compact('nettingsData','transfer_products','transfer_materials'));


    }
    public function transferShow($id){
        $transfer = Transfer::with('company')->where('id',$id)->first();
        return $transfer_products = ProductTransfer::with('product_transfer_detail','product')->where('transfer_id','=',$id)->get();
        $transfer_materials = MaterialTransfer::with('detail','material')->where('transfer_id','=',$id)->get();
        return view('admin.dyeing.show',compact('transfer','transfer_products','transfer_materials'));

    }

    public function transferProduct($id)
    {
    //    $rest_quantity = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query) use($id){
    //        $query->where('company_id', '=', $id)->where('department_id',2);
    //    })->get();

      $rest_quantity = ProductTransfer::with('transfer')->where('rest_quantity','>',0)->whereHas('transfer', function (Builder $query) use($id){
        $query->where('company_id', '=', $id)->where('department_id',2);
        })->get()->groupBy('product_id');

       $materials = ProductTransfer::with('transfer','product')->whereHas('transfer', function (Builder $query) use($id){
           $query->where('company_id', '=', $id);
       })->get()->pluck('product.name','product.id')->prepend(trans('global.pleaseSelect'),'');
       $colors = MaterialConfig::where('type',3)->pluck('name','id')->prepend(trans('global.pleaseSelect'));
       $company_id = $id;
       $material_key_by = MaterialConfig::get()->keyBy('id');
       return view('admin.showroom.stock-transfer',compact('rest_quantity','materials','colors','company_id','material_key_by'));
    }

    public function expenses(){
        $expenses = Expense::where('department_id',2)->get();

        return view('admin.expenses.index', compact('expenses'));
    }
    public function search(Request $request){
        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $company_id = $request->company_id;
        $materials = MaterialConfig::where('type',1)->get();
        $color_id = $request->color_id;
        $process_fee = $request->process_fee;

        $check_quantity = $this->checkKneetingProductQuantity($request);
        if(!$check_quantity){
            $material_name = MaterialConfig::find($product_id);
            return ['status' => 103, 'message' => "Sorry !!!  ".$material_name->name." Low Stock"];
        }
        return view('admin.dyeing.include.stock-list',compact('materials','product_id','quantity','company_id','color_id','process_fee'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $product_id = $request->product_id;
            $quantity = $request->quantity;
            $company_id = $request->company_id;
            $color_id = $request->color_id;
            $process_fee = $request->process_fee;

            $check_company_type = Company::where('id', $company_id)->first();


            $transfer = new Transfer();
            $transferData['company_id'] = $company_id;
            $transferData['department_id'] = 2;
            $transferData['created_by'] = Auth::user()->id;
            $transferData['date'] = date("Y-m-d");
            $storeTransfer = $transfer->create($transferData);

            $transfer_id = $storeTransfer->id;
            if($check_company_type->type == 1){
                $company = $request->company_id;
                $product = $request->product_id;

               $getAllStock = ProductTransfer::with('transfer')->whereHas('transfer', function (Builder $query) use($company,$product){
                    $query->where('company_id', '=', $company)->where('product_id', '=', $product)->where('rest_quantity','>',0);
                })->get();
                $contentQty = $quantity;
                foreach ($getAllStock as $stock)
                {
                    $pro_qty = $contentQty;
                    if($stock->rest_quantity < $contentQty) {
                        $pro_qty = $stock->rest_quantity;
                        $contentQty = $contentQty - $stock->rest_quantity;

                        $productTransfer = new ProductTransfer();
                        $transferProduct['product_id'] = $product_id;
                        $transferProduct['quantity'] = $pro_qty;
                        $transferProduct['rest_quantity'] = $pro_qty;
                        $transferProduct['transfer_id'] = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['color_id'] = $color_id;
                        $transferProduct['process_fee'] = $process_fee;
                        $transferProduct['created_by'] = Auth::user()->id;
                        $storeTransfer = $productTransfer->create($transferProduct);


                        if($storeTransfer){
                            // reduce stock quantity
                            $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                            DB::table('product_transfer')->where('id',$stock->id)->update($data);
                            logger(' Updated rest qty 1'.$data['rest_quantity']);

                            // Add expense

                            $expense = new Expense();
                            $expense->entry_date = date("Y-m-d");
                            $expense->amount = ($pro_qty*$stock->process_fee);
                            $expense->description = "Product Process Costing of Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id = 1;
                            $expense->transfer_id = $transfer_id;
                            $expense->created_by_id = Auth::user()->id;
                            $expense->material_id = $stock->product_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                            logger('Expense 1'.$expense);
                        }
                    } else {
                        $contentQty = 0;
                        $productTransfer = new ProductTransfer();
                        $transferProduct['product_id'] = $product_id;
                        $transferProduct['quantity'] = $pro_qty;
                        $transferProduct['rest_quantity'] = $pro_qty;
                        $transferProduct['transfer_id'] = $transfer_id;
                        $transferProduct['product_stock_id'] = $stock->id;
                        $transferProduct['color_id'] = $color_id;
                        $transferProduct['process_fee'] = $process_fee;
                        $transferProduct['created_by'] = Auth::user()->id;
                        $storeTransfer = $productTransfer->create($transferProduct);
                        if($storeTransfer){
                            // reduce stock quantity
                            $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                            DB::table('product_transfer')->where('id',$stock->id)->update($data);
                            logger(' Updated rest qty 2'.$data['rest_quantity']);


                            // Add expense

                            $expense = new Expense();
                            $expense->entry_date = date("Y-m-d");
                            $expense->amount = ($pro_qty*$stock->process_fee);
                            $expense->description = "Product Process Costing of Netting";
                            $expense->expense_category_id = 1;
                            $expense->department_id = 1;
                            $expense->transfer_id = $transfer_id;
                            $expense->created_by_id = Auth::user()->id;
                            $expense->material_id = $stock->product_id;
                            $expense->transfer_product_id = $storeTransfer->id;
                            $expense->save();
                            logger('Expense 2'.$expense);

                        }
                    }

                    if ($contentQty < 1) {
                        break;
                    }

                }
            }


            foreach ($request->material_qty as $key => $material) {
                if ($material != null) {
                    $materialRequest['product_id'] = $key;
                    $materialRequest['quantity'] = $material;
                    $material_name = MaterialConfig::find($key);
                    $checkMaterialQty = $this->checkMaterialQuantity($materialRequest);
                    if (!$checkMaterialQty) {
                        DB::rollback();
                        return ['status' => 104, 'message' => "Sorry !!!  ".$material_name->name." Low Stock"];
                    }
                    $total_material_stocks = MaterialIn::where('material_id',$key)->get();

                    $contentQty = $quantity;
                    foreach ($total_material_stocks as $stock)
                    {
                        $pro_qty = $material;
                        if($stock->rest < $pro_qty){
                            $pro_qty = $stock->rest;
                            $contentQty = $contentQty - $stock->rest;

                            $materialStore = new MaterialTransfer();
                            $materialStore->material_id = $key;
                            $materialStore->transfer_id = $transfer_id;
                            $materialStore->quantity = $pro_qty;
                            $materialStore->material_stock_id = $stock->id;
                            $materialStore->created_by = Auth::user()->id;
                            $materialDataStore = $materialStore->save();

                            if($materialDataStore){
                                // reduce stock quantity
                                $material_data['rest'] = $stock->rest - $pro_qty;
                                DB::table('material_ins')->where('id',$stock->id)->update($material_data);

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($pro_qty*$stock->unit_price);
                                $expense->description = "Material Costing of Dyeing";
                                $expense->expense_category_id = 2;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->id;
                                $expense->transfer_product_id = $storeTransfer->id;
                                $expense->save();
                            logger('Expense 3'.$expense);

                            }
                        }
                        else {
                            $contentQty = 0;
                            $materialStore = new MaterialTransfer();
                            $materialStore->material_id = $key;
                            $materialStore->transfer_id = $transfer_id;
                            $materialStore->quantity = $pro_qty;
                            $materialStore->material_stock_id = $stock->id;
                            $materialStore->created_by = Auth::user()->id;
                            $materialDataStore = $materialStore->save();

                            if($materialDataStore){
                                // reduce stock quantity
                                $material_data['rest'] = $stock->rest - $pro_qty;
                                DB::table('material_ins')->where('id',$stock->id)->update($material_data);

                                // Add expense

                                $expense = new Expense();
                                $expense->entry_date = date("Y-m-d");
                                $expense->amount = ($pro_qty*$stock->unit_price);
                                $expense->description = "Material Costing of Dyeing";
                                $expense->expense_category_id = 2;
                                $expense->department_id = 2;
                                $expense->transfer_id = $transfer_id;
                                $expense->created_by_id = Auth::user()->id;
                                $expense->material_id = $stock->id;
                                $expense->transfer_product_id = $storeTransfer->id;
                                $expense->save();

                            logger('Expense 4'.$expense);

                            }
                        }
                        if ($contentQty < 1) {
                            break;
                        }
                    }

                }
            }
            DB::commit();

            return ['status'=>200,'message'=>'Successfully Transfer'];
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
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

    private function checkKneetingProductQuantity($request){
        $company = $request->company_id;
        $product_id = $request->product_id;
        $total_stock_qty = ProductTransfer::where('product_id',$product_id)->with('transfer')->whereHas('transfer', function (Builder $query) use($company){
            $query->where('company_id', '=', $company)->where('department_id',1);
        })->sum('rest_quantity');
        if($total_stock_qty > $request->quantity) {
             return true;
        }
        else{
             return false;
        }
     }
     private function checkMaterialQuantity($request){
        $total_stock_qty = MaterialIn::where('material_id',$request['product_id'])->sum('rest');
        if($total_stock_qty > $request['quantity']) {
            return true;
        }
        else{
            return false;
        }
    }
}
