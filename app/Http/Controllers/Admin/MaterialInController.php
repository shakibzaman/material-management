<?php

namespace App\Http\Controllers\Admin;

use App\Department;
use App\Employee;
use App\Http\Controllers\Controller;
use App\MaterialConfig;
use App\MaterialIn;
use App\Payment;
use App\ProductTransfer;
use App\Supplier;
use App\SupplierProduct;
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

        return view('admin.material.index', compact('materials','materialsPurchased'));
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
        $user_account_detail = UserAccount::where('user_id',$request->supplier_id)->first();
        if(!$user_account_detail){
            return ['status'=>105,'message'=>'Sorry your Supplier not founded'];
        }
        $request['created_by'] = Auth::user()->id;
        $request['rest'] = $request->quantity;
        $materialIn = MaterialIn::create($request->all());

        if($materialIn){
            $request['material_in_id'] = $materialIn->id;
            $request['payment_process'] = $request->payment_process;
            $request['payment_info'] = $request->payment_info;
            SupplierProduct::create($request->all());


            if($request->paid_amount>0){
                $request['amount'] = $request->paid_amount;
                $request['user_account_id'] = $user_account_detail->id;
                Payment::create($request->all());
            }


            $user_account['total_due'] = $user_account_detail->total_due + ($request->total_price - $request->paid_amount);
            $user_account['total_paid'] = $user_account_detail->total_paid + $request->paid_amount;
            $user_account_detail->update($user_account);

            }
            DB::commit();
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $materials = MaterialIn::with('user','employee','units')->where('material_id',$id)->get();
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
    public function search($id){
        $material = MaterialConfig::where('id',$id)->first();

         $transfer_product_color = ProductTransfer::with('transfer','color')->where('product_id',$material->id)
            ->where('rest_quantity','>',0)
            ->whereHas('transfer', function (\Illuminate\Database\Eloquent\Builder $query){
            $query->where('department_id', 2);
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
