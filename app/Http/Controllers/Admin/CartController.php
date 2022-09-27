<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Expense;
use App\Http\Controllers\Controller;
use App\MaterialIn;
use App\Order;
use App\OrderDetail;
use App\Payment;
use App\ProductTransfer;
use App\UserAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cart(){
        $materials = MaterialIn::with('material')->where('type',2)->get()->pluck('material.name','material.id')->prepend('Pleease Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart',compact('materials','customers'));
    }
    public function showroomCartOrder(Request $request){
        $department_id = 3;
        $validated = $request->validate([
            'invoice_id' => 'required|unique:orders|max:255',
            'customer_id' => 'required',
            'payment_process' => 'required',
            'payment_info' => 'required',
        ]);



        DB::beginTransaction();
        try{
            $request['created_by'] = Auth::user()->id;
            $request['department_id'] = $department_id;
            $order = Order::create($request->all());

            // Order create Done

            // Order details create start
            for($i=0;$i<count($request->material_id);$i++){
                $getAllStock = ProductTransfer::with('transfer')
                    ->where('product_id',$request->material_id[$i])
                    ->where('color_id',$request->color_id[$i])
                    ->where('rest_quantity','>',0)
                    ->whereHas('transfer', function (Builder $query) use ($department_id){
                        $query->where('department_id',$department_id );
                    })->get();
                // Stock deduct from product supply start
                $quantity = $request->quantity[$i];
                $contentQty = $quantity;
                foreach ( $getAllStock as $stock ) {
                    $pro_qty = $contentQty;
                    if ( $stock->rest_quantity < $contentQty ) {
                        $pro_qty    = $stock->rest_quantity;
                        $contentQty = $contentQty - $stock->rest_quantity;

                        // reduce stock quantity
                        $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                        DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                        logger( ' Updated rest qty 1' . $data['rest_quantity'] );

                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  $request->material_id[$i];
                        $cart->color_id = $request->color_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->qty = $request->quantity[$i];
                        $cart->line_total = $request->line_total[$i];
                        $cart->save();


                    } else {
                        $contentQty                          = 0;

                        // reduce stock quantity
                        $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                        DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                        logger( ' Updated rest qty 2' . $data['rest_quantity'] );

                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  $request->material_id[$i];
                        $cart->color_id = $request->color_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->qty = $request->quantity[$i];
                        $cart->line_total = $request->line_total[$i];
                        $cart->save();
                    }

                    if ( $contentQty < 1 ) {
                        break;
                    }

                }
                // Stock deduct from product supply end

            }
            // Order details create end

            // User account update start
            $user_account = UserAccount::where('user_id',$request->customer_id)->where('type',2)->first();
            if($request->due>0){
                $account['total_due'] = $user_account->total_due + $request->due;
                $user_account->update($account);
                logger("Due updated");
            }
            if($request->paid>0){
                // Payment data store start
                $payment                  = new Payment();
                $payment->amount          = $request->paid;
                $payment->payment_process = $request->payment_process;
                $payment->payment_info    = $request->payment_info;
                $payment->user_account_id = $user_account->id;
                $payment->created_by = Auth::user()->id;
                $payment->save();
                logger("Payment Updated");
            }

            // User account update end


            DB::commit();
            return ['status'=>200,'message'=>"Order Created Successful"];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
