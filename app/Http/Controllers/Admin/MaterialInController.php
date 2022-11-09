<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Department;
use App\Employee;
use App\Fund;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\Payment;
use App\Product;
use App\ProductTransfer;
use App\Supplier;
use App\SupplierProduct;
use App\Transaction;
use App\Unit;
use App\User;
use App\UserAccount;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Support\Facades\Auth;
use PhpParser\Builder;
use Symfony\Component\HttpFoundation\Response;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

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
//       $materials = MaterialConfig::all()->pluck('name','id')->prepend( 'Please Select', '' );;

        return view('admin.material.index', compact('materials','materialsPurchased'));
    }

    public function addToPurchase($id){
        $material_config = MaterialConfig::where('id',$id)->first();
        $html = '<tr>
    <td>
        <input type="text" class="form-control" value="'.$material_config->name.'" name="material_name[]">
        <input type="hidden" class="form-control material_id" value="'.$material_config->id.'" name="material_id[]">
    </td>
    <td>
        <input type="text" class="form-control quantity" value="" name="quantity[]">
    </td>
    <td>
        <input type="text" class="form-control price" value="" name="price[]">
    </td>
    <td>
        <input type="text" class="form-control line_total" value="" name="line_total[]">
    </td>
    <td class="actions" data-th="">
        <button class="btn btn-danger btn-sm remove-from-cart"><i class="fa fa-trash-o"></i></button>
    </td>
</tr>';
        return response()->json(['html'=>$html]);
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
        $suppliers = Supplier::all()->pluck( 'name', 'id' )->prepend( trans( 'global.pleaseSelect' ), '' );

        return view('admin.material.create', compact('employees','materials','units','suppliers'));

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


        $user_account_detail = UserAccount::where('user_id',$request->supplier_id)->where('type',1)->first();
        if(!$user_account_detail){
            return ['status'=>105,'message'=>'Sorry your Supplier not founded'];
        }

        $invoice_store = makeInvoice($request);
        logger('Invoice Make done');
        logger('Invoice Id '.$invoice_store);
        if(!$invoice_store)
        {
            return ['status'=>103,'message'=>'Invoice store failed'];
        }


        for($i=0;$i<count($request->material_id);$i++){
            $material = new MaterialIn();
            $material->material_id = $request->material_id[$i];
            $material->quantity = $request->quantity[$i];
            $material->unit_price = $request->price[$i];
            $material->total_price = $request->line_total[$i];
            $material->unit = 1;
            $material->buying_date = $request->buying_date;
            $material->type = $request->type;
            $material->supplier_id = $request->supplier_id;
            $material->created_by = Auth::user()->id;
            $material->rest = $request->quantity[$i];
            $material->purchased_by = Auth::user()->id;
            $material->inv_number = $request->inv_number;
            $materialIn = $material->save();

            logger('material save');
            if(!$materialIn)
            {
                return ['status'=>103,'message'=>'Material In store failed'];
            }

            if($materialIn) {
                // Supplier Bill start
                $supplier_product_store = new SupplierProduct();
                $supplier_product_store->material_in_id = $material->id;
                $supplier_product_store->material_id = $request->material_id[$i];
                $supplier_product_store->quantity = $request->quantity[$i];
                $supplier_product_store->supplier_id = $request->supplier_id;
                $supplier_product_data_store = $supplier_product_store->save();
                logger(' Supplier product save');
                if(!$supplier_product_data_store)
                {
                    return ['status'=>103,'message'=>'supplier_product_data_store failed'];
                }
            }
        }
        if($request->paid_amount>0){

            $payment = new Payment();
            $payment->amount = $request->paid_amount;
            $payment->payment_process = $request->payment_process ?? 'N/A';
            $payment->user_account_id = $user_account_detail->id;
            $payment->releted_id = $invoice_store;
            $payment->releted_department_id = 5;
            $payment->releted_id_type = 2;
            $payment->created_by = Auth::user()->id;
            $payment_store = $payment->save();
            logger('Payment save');

            if(!$payment_store)
            {
                return ['status'=>103,'message'=>'Payment store failed'];
            }


            if($request->payment_process == 'bank'){
                $bank_info = Bank::where('id',$request->payment_type)->first();
                logger('Bank info got');
                if($bank_info->current_balance < $request->paid_amount){
                    DB::rollback();
                    return ['status'=>103,'message'=>'Sorry Bank amount low'];
                }
                $bank['current_balance'] = $bank_info->current_balance - $request->paid_amount;
                $bank_info->update($bank);

                $transaction = new Transaction();
                $transaction->bank_id = $bank_info->id;
                $transaction->date = now();
                $transaction->source_type = 1; // 2 is account 1 is bank
                $transaction->type = 1; // 1 is Widthrow
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->paid_amount;
                $transaction->reason = 'Supplier Payment Material';
                $transaction->created_by = Auth::user()->id;
                $transaction->save();

                logger('Transaction store');

            }
            if($request->payment_process == 'account'){
                $fund_info = Fund::where('id',$request->payment_type)->first();

                logger('Fund info got');

                if($fund_info->current_balance < $request->paid_amount){
                    DB::rollback();
                    return ['status'=>103,'message'=>'Sorry Store amount low'];
                }
                $fund['current_balance'] = $fund_info->current_balance - $request->paid_amount;
                $fund_info->update($fund);

                $transaction = new Transaction();
                $transaction->bank_id = $fund_info->id;
                $transaction->source_type = 2; // 2 is account 1 is bank
                $transaction->type = 1;
                $transaction->date = now();
                $transaction->payment_id = $payment->id;
                $transaction->amount = $request->paid_amount;
                $transaction->reason = 'Supplier Payment for finish Product';
                $transaction->created_by = Auth::user()->id;
                $transaction->save();
                logger('Transaction Store');

            }
        }

        $user_account['total_due'] = $user_account_detail->total_due + ($request->total_price - $request->paid_amount);
        $user_account['total_paid'] = $user_account_detail->total_paid + $request->paid_amount;
        $user_account_detail->update($user_account);

        logger('Account updated');

            DB::commit();
            logger('Commit done');
        } catch (\Exception $e) {

            DB::rollback();
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


//        DB::beginTransaction();
//        try {
//            for($i=0;$i<count($request->material_id);$i++) {
//                logger('i is ' .$i.'and material is '.$request->material_id[$i]);
//                $user_account_detail = UserAccount::where('user_id', $request->supplier_id)->where('type',1)->first();
//                if (!$user_account_detail) {
//                    return ['status' => 105, 'message' => 'Sorry your Supplier not founded'];
//                }
//
//                logger(' Material In start');
//                $material = new MaterialIn();
//                $material->material_id = $request->material_id[$i];
//                $material->quantity = $request->quantity[$i];
//                $material->unit_price = $request->price[$i];
//                $material->total_price = $request->line_total[$i];
//                $material->unit = 1;
//                $material->type = $request->type;
//                $material->supplier_id = $request->supplier_id;
//                $material->created_by = Auth::user()->id;
//                $material->rest = $request->quantity[$i];
//                $material->purchased_by = Auth::user()->id;
//                $material->inv_number = $request->inv_number;
//                $material->save();
//                $materialIn = $material->id;
//                logger('Material Store');
//
//                if ($materialIn) {
//                    $supplier_product_store = new SupplierProduct();
//                    $supplier_product_store->material_in_id = $material->id;
//                    $supplier_product_store->material_id = $request->material_id[$i];
//                    $supplier_product_store->quantity = $request->quantity[$i];
//                    $supplier_product_store->supplier_id = $request->supplier_id;
//                    $supplier_product_store->paid_amount = $request->paid_amount ?? 0;
//                    $supplier_product_store->due_amount = $request->due_amount ?? 0;
//                    $supplier_product_store->payment_process = $request->payment_process;
//                    $supplier_product_store->payment_info = $request->payment_info;
//                    $supplier_product_store->save();
//
//                    logger('supplier_store');
//
//
//                    if ($request->paid_amount > 0) {
//                        $request['amount'] = $request->paid_amount;
//                        $request['user_account_id'] = $user_account_detail->id;
//                        $request['releted_id'] = $supplier_product_store->id;
//                        $request['releted_id_type'] = 2;
//                        $request['releted_department_id'] = 5;
//                        $request['created_by'] = Auth::user()->id;
//                        $payment = Payment::create($request->all());
//
//                        if($request->payment_process !=null){
//                            if ($request->payment_process == 'bank') {
//                                $bank_info = Bank::where('id', $request->payment_type)->first();
//                                $bank['current_balance'] = $bank_info->current_balance - $request->paid_amount;
//                                $bank_info->update($bank);
//
//                                $transaction = new Transaction();
//                                $transaction->bank_id = $bank_info->id;
//                                $transaction->date = now();
//                                $transaction->source_type = 1; // 2 is account 1 is bank
//                                $transaction->type = 1; // 1 is Widthrow
//                                $transaction->payment_id = $payment->id;
//                                $transaction->amount = $request->paid_amount;
//                                $transaction->reason = 'Supplier Payment';
//                                $transaction->created_by = Auth::user()->id;
//
//                                $transaction->save();
//
//                            }
//                            if ($request->payment_process == 'account') {
//                                $fund_info = Fund::where('id', $request->payment_type)->first();
//                                $fund['current_balance'] = $fund_info->current_balance - $request->paid_amount;
//                                $fund_info->update($fund);
//
//                                $transaction = new Transaction();
//                                $transaction->bank_id = $fund_info->id;
//                                $transaction->source_type = 2; // 2 is account 1 is bank
//                                $transaction->type = 1;
//                                $transaction->date = now();
//                                $transaction->payment_id = $payment->id;
//                                $transaction->amount = $request->paid_amount;
//                                $transaction->reason = 'Supplier Payment';
//                                $transaction->created_by = Auth::user()->id;
//
//                                $transaction->save();
//
//                            }
//                        }
//
//                    }
//                    logger('Account amount added');
//                    $user_account['total_due'] = $user_account_detail->total_due + ($request->total_price - $request->paid_amount);
//                    $user_account['total_paid'] = $user_account_detail->total_paid + $request->paid_amount;
//                    $user_account_detail->update($user_account);
//                    logger('Account amount added done');
//                }
//            }
//            DB::commit();
//            logger('Commit done');
//        } catch (\Exception $e) {
//
//            DB::rollback();
//            return $e->getMessage();
//        }
//        if($request->type == 1)
//        {
//            return redirect()->route('admin.material-in.index');
//        }
//        if($request->type == 2)
//        {
//            return redirect()->route('admin.product.purchase');
//        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $materials = MaterialIn::with('user','employee','units','supplier')->where('material_id',$id)->get();
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
    public function search($department_id,$id){
        $department_id = $department_id;
        $material = MaterialConfig::where('id',$id)->first();

         $transfer_product_color = ProductTransfer::with('transfer','color')->where('product_id',$material->id)
            ->where('rest_quantity','>',0)
            ->whereHas('transfer', function (\Illuminate\Database\Eloquent\Builder $query) use ($department_id){
            $query->where('department_id', $department_id);
        })->get()->pluck('color.id');
        $color = MaterialConfig::whereIn('id',$transfer_product_color)->get();

        return ['material'=>$material,'color'=>$color];
    }

    public function searchwithColor($id,$color_id){
        $color = MaterialConfig::where('id',$color_id)->first();
        $material = MaterialConfig::where('id',$id)->first();
        $material['color_name']=$color->name;
        $material['color_id']=$color->id;

        return $material;
    }
    public function price($id){
        $price = MaterialConfig::where('id',$id)->first();
        return view('admin.material.modal.price',compact('price'));
    }

    public function storePrice(Request $request){
        $price = MaterialConfig::where('id',$request->material_id)->first();
        DB::beginTransaction();
        try{
            $data['material_price']  = $request->material_price;
            $data['knitting_price']  = $request->knitting_price;
            $data['selling_price']  = $request->selling_price;
            $update_price = $price->update($data);
            DB::commit();
            if($update_price){
                return redirect()->back()->with('message','Price Updated');
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }


    }
}
