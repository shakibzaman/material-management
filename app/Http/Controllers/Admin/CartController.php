<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Department;
use App\Expense;
use App\Http\Controllers\Controller;
use App\MaterialIn;
use App\Order;
use App\OrderDetail;
use App\Payment;
use App\Product;
use App\ProductTransfer;
use App\UserAccount;
//use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cart($id){

        $carts = Cart::content();
        $department_id = $id;
        $department = Department::where('id',$id)->first();
        $materials = ProductTransfer::with('transfer','product')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->pluck('product.name','product.id')->prepend( trans( 'global.pleaseSelect' ), '' );

        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart',compact('materials','customers','department','carts'));
    }

    public function knittingcart($id){
        $carts = Cart::content();
        $department = Department::where('id',$id)->first();
        $materials = ProductTransfer::with('transfer','product')->whereHas('transfer', function (Builder $query) use ($id){
            $query->where('department_id', $id);
        })->get()->pluck('product.name','product.id')->prepend( trans( 'global.pleaseSelect' ), '' );

        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.neeting.cart.cart',compact('materials','customers','department','carts'));
    }
    public function pos(){
        $materials = MaterialIn::with('material')->where('type',2)->get()->pluck('material.name','material.id')->prepend('Pleease Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.include.cart',compact('materials','customers'));
    }
    public function index()
    {
        $products = Product::all();
        return view('admin.cart.include.products', compact('products'));
    }

    public function cartTest(){
        $materials = MaterialIn::with('material')->where('type',2)->get()
            ->pluck('material.name','material.id')->prepend('Pleease Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart-test',compact('materials','customers'));
    }
    public function addToCartTest($department_id,$id,$color)
    {
        $department_id = $department_id;
       $products = DB::table('product_transfer')
            ->join('material_configs AS product','product_transfer.product_id','product.id')
            ->join('material_configs AS color','product_transfer.color_id','color.id')
            ->join('transfer','product_transfer.transfer_id','transfer.id')
            ->where('product_transfer.product_id',$id)
            ->where('product_transfer.color_id',$color)
            ->where('product_transfer.rest_quantity','>',0)
            ->where('transfer.department_id',$department_id)
            ->select('product_transfer.product_id','product_transfer.color_id','product.name AS product_name','color.name AS color_name','product.selling_price')
            ->first();

       if( count(Cart::content()) > 0){
           logger("Cart not empty");
           foreach(Cart::content() as $row) {
               logger("Row Product Id".$row->id.' org Product Id '.$products->product_id." Row Color Id ".$row->options->color_id." Orginal Color id ".$products->color_id);
               logger('Color id '.$row->options->color_id);
               if($row->id == $products->product_id && $row->options->color_id == $products->color_id){
                   logger("Old");
                   Cart::update($row->rowId, $row->qty+1);
               }
               else{
                   logger("cart New");
                   Cart::add([
                       ['id' => $products->product_id, 'name' => $products->product_name, 'qty' => 1,'price'=>$products->selling_price,
                           'options' => ['color'=>$products->color_name,'color_id'=>$products->color_id,'sub_total'=>$products->selling_price,'product_id'=>$products->product_id]]
                   ]);
               }
           }
       }
       else{
           logger("New");
           Cart::add([
               ['id' => $products->product_id, 'name' => $products->product_name, 'qty' => 1,'price'=>$products->selling_price,
                   'options' => ['color'=>$products->color_name,'color_id'=>$products->color_id,'sub_total'=>$products->selling_price,'product_id'=>$products->product_id]]
           ]);

       }
       logger("cart is ".Cart::content());
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    public function addToCartKnitting($department_id,$id)
    {
        $department_id = $department_id;
       $products = DB::table('product_transfer')
            ->join('material_configs AS product','product_transfer.product_id','product.id')
            ->join('transfer','product_transfer.transfer_id','transfer.id')
            ->where('product_transfer.product_id',$id)
            ->where('product_transfer.rest_quantity','>',0)
            ->where('transfer.department_id',$department_id)
            ->where('transfer.company_id',1)
            ->select('product_transfer.product_id','product.name AS product_name','product.knitting_price')
            ->first();

       if( count(Cart::content()) > 0){
           foreach(Cart::content() as $row) {
               if($row->id == $products->product_id){
                   Cart::update($row->rowId, $row->qty+1);
               }
               else{
                   Cart::add([
                       ['id' => $products->product_id, 'name' => $products->product_name, 'qty' => 1,'price'=>$products->knitting_price,
                           'options' => ['sub_total'=>$products->knitting_price,'product_id'=>$products->product_id]]
                   ]);
               }
           }
       }
       else{
           Cart::add([
               ['id' => $products->product_id, 'name' => $products->product_name, 'qty' => 1,'price'=>$products->knitting_price,
                   'options' => ['sub_total'=>$products->knitting_price,'product_id'=>$products->product_id]]
           ]);

       }
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function addToCart($id)
    {
//        $product = Product::findOrFail($id);
//
//        $cart = session()->get('cart', []);
//
//        if(isset($cart[$id])) {
//            $cart[$id]['quantity']++;
//        } else {
//            $cart[$id] = [
//                "name" => $product->name,
//                "quantity" => 1,
//                "price" => $product->price,
//                "image" => $product->image
//            ];
//        }
//
//        session()->put('cart', $cart);
//        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $rowId = $request->id;
            Cart::update($rowId, $request->quantity);
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if($request->id) {
            $rowId = $request->id;
            Cart::remove($rowId);
        }
    }
    public function showroomCartOrder(Request $request){
        $department_id = $request->department_id;
        $validated = $request->validate([
            'invoice_id' => 'required|unique:orders|max:255',
            'customer_id' => 'required',
            'payment_process' => 'required',
            'payment_info' => 'required',
        ]);

        $low_stock = [];
        for($i=0;$i<count($request->material_id);$i++) {
            $getAllStock = ProductTransfer::with('transfer')
                ->where('product_id', $request->material_id[$i])
                ->where('color_id', $request->color_id[$i])
                ->where('rest_quantity', '>', 0)
                ->whereHas('transfer', function (Builder $query) use ($department_id) {
                    $query->where('department_id', $department_id);
                })->get()->sum('rest_quantity');
            if($getAllStock < $request->quantity[$i]){
                logger("Low Stock");
                    array_push($low_stock,$request->material_id[$i]);
            }

        }
        if(count($low_stock)>0){
            return ['status' => 104, 'message' => "Sorry !!!  Low Stock"];

        }

        DB::beginTransaction();
        try{
            $request['created_by'] = Auth::user()->id;
            $request['department_id'] = $department_id;
            $order = Order::create($request->all());
            logger("Order Create");
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
                        $cart->selling_price = $request->selling_price[$i];
                        $cart->line_total = $request->line_total[$i];
                        $cart->save();


                    } else {
                        $contentQty                          = 0;

                        // reduce stock quantity
                        $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                        DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                        logger( ' Updated rest qty 2' . $data['rest_quantity'] );
                        logger("Order details".$order->id."Material -id: ".$request->material_id[$i]."Color - ".$request->color_id[$i]."Stock ".$stock->id);
                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  $request->material_id[$i];
                        $cart->color_id = $request->color_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->selling_price = $request->selling_price[$i];
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
            logger("Order Details");
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
                $payment->releted_id = $order->id;
                $payment->releted_id_type = 1;
                $payment->created_by = Auth::user()->id;
                $payment->save();
                logger("Payment Updated");
            }

            // User account update end

            DB::commit();
            session()->forget('cart');
            return ['status'=>200,'message'=>"Order Created Successful"];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function KnittingCartOrder(Request $request){
        $department_id = $request->department_id;
        $validated = $request->validate([
            'invoice_id' => 'required|unique:orders|max:255',
            'customer_id' => 'required',
            'payment_process' => 'required',
            'payment_info' => 'required',
        ]);

        $low_stock = [];
        for($i=0;$i<count($request->material_id);$i++) {
            $getAllStock = ProductTransfer::with('transfer')
                ->where('product_id', $request->material_id[$i])
                ->where('rest_quantity', '>', 0)
                ->whereHas('transfer', function (Builder $query) use ($department_id) {
                    $query->where('department_id', $department_id);
                })->get()->sum('rest_quantity');
            if($getAllStock < $request->quantity[$i]){
                logger("Low Stock");
                    array_push($low_stock,$request->material_id[$i]);
            }

        }
        if(count($low_stock)>0){
            return ['status' => 104, 'message' => "Sorry !!!  Low Stock"];

        }

        DB::beginTransaction();
        try{
            $request['created_by'] = Auth::user()->id;
            $request['department_id'] = $department_id;
            $order = Order::create($request->all());
            logger("Order Create");
            // Order create Done

            // Order details create start
            for($i=0;$i<count($request->material_id);$i++){
                $getAllStock = ProductTransfer::with('transfer')
                    ->where('product_id',$request->material_id[$i])
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
                        logger( ' Updated rest qty 1)' . $data['rest_quantity'] );
                        logger("Ok");


                        logger(['Order '.$order->id." Material ".$request->material_id[$i]." Stock ".$stock->id." Selling ".$request->selling_price[$i]." Qty ".$request->quantity[$i]."Line total".$request->line_total[$i]]);

                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  $request->material_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->qty = $request->quantity[$i];
                        $cart->selling_price = $request->selling_price[$i];
                        $cart->line_total = $request->line_total[$i];
                        $cart->save();


                    } else {
                        $contentQty                          = 0;

                        // reduce stock quantity
                        $data['rest_quantity'] = $stock->rest_quantity - $pro_qty;
                        DB::table( 'product_transfer' )->where( 'id', $stock->id )->update( $data );
                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  $request->material_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->selling_price = $request->selling_price[$i];
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
            logger("Order Details");
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
                $payment->releted_id = $order->id;
                $payment->releted_id_type = 1;
                $payment->created_by = Auth::user()->id;
                $payment->save();
                logger("Payment Updated");
            }

            // User account update end

            DB::commit();
            session()->forget('cart');
            return ['status'=>200,'message'=>"Order Created Successful"];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
