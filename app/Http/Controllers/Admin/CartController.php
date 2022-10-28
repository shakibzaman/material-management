<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\Customer;
use App\Department;
use App\Expense;
use App\Fund;
use App\Http\Controllers\Controller;
use App\MaterialIn;
use App\Order;
use App\OrderDetail;
use App\Payment;
use App\Product;
use App\ProductTransfer;
use App\Transaction;
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
        $department = Department::where('id',$id)->first();

        $materials = Product::with('color')->where('showroom_id',$id)->get()
            ->pluck('color_id','color.name')->prepend( 'Please Select', '' );
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart-test',compact('materials','customers','department'));

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
        $materials = MaterialIn::with('material')->where('type',2)->get()->pluck('material.name','material.id')->prepend('Please Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.include.cart',compact('materials','customers'));
    }
    public function index()
    {
        $products = Product::all();
        return view('admin.cart.include.products', compact('products'));
    }

    public function cartTest(){
        $materials = Product::with('color')->where('showroom_id',3)->get()->pluck('color_id','color.name')->prepend( 'Please Select', '' );
//        $materials = MaterialIn::with('material')->where('type',2)->get()
//            ->pluck('material.name','material.id')->prepend('Pleease Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart-test',compact('materials','customers'));
    }

    public function addToCartProduct($department_id,$color){
        $department_id = $department_id;
        $material = Product::with('color')->where(['color_id'=>$color,'showroom_id'=>$department_id])->where('quantity','>',0)->first();

        $html = '<tr>
    <td>
        <input type="text" class="form-control" value="'.$material->color->name.'" name="material_name[]">
        <input type="hidden" class="form-control material_id" value="'.$material->color->id.'" name="color_id[]">
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
    public function addToCartTest($department_id,$color)
    {
        $department_id = $department_id;
        $products = Product::with('color')->where(['color_id'=>$color,'showroom_id'=>$department_id])->where('quantity','>',0)->first();

       if( count(Cart::content()) > 0){
           logger("Cart not empty");
           foreach(Cart::content() as $row) {
//               logger("Row Product Id".$row->id.' org Product Id '.$products->product_id." Row Color Id ".$row->options->color_id." Orginal Color id ".$products->color_id);
//               logger('Color id '.$row->options->color_id);
               if($row->id == $products->color_id){
                   logger("Old");
                   Cart::update($row->rowId, $row->qty+1);
               }
               else{
                   logger("cart New");
                   Cart::add([
                       ['id' => $products->color_id, 'name' => $products->color->name, 'qty' => 1,'price'=>$products->process_costing,
                           'options' => ['color'=>$products->color->name,'color_id'=>$products->color_id,'sub_total'=>$products->process_costing]]
                   ]);
               }
           }
       }
       else{
           logger("New");
           Cart::add([
               ['id' => $products->color_id, 'name' => $products->color->name, 'qty' => 1,'price'=>$products->process_costing,
                   'options' => ['color'=>$products->color->name,'color_id'=>$products->color_id,'sub_total'=>$products->process_costing]]
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
    public function updatePrice(Request $request)
    {
        if ($request->id && $request->price) {
            $rowId = $request->id;
            Cart::update($rowId,['price' => $request->price] );
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
            'customer_id' => 'required'
        ]);

        $low_stock = [];
        for($i=0;$i<count($request->color_id);$i++) {
           $getAllStock = Product::where('color_id',$request->color_id[$i])
                ->where('quantity', '>', 0)->get()
                ->sum('quantity');

            if($getAllStock < $request->quantity[$i]){
                logger("Low Stock");
                    array_push($low_stock,$request->color_id[$i]);
            }

        }
        if(count($low_stock)>0){
            return ['status' => 104, 'message' => "Sorry !!!  Low Stock"];

        }

        DB::beginTransaction();
        try{
            $request['paid'] = $request->paid ?? 0;
            $request['due'] = $request->due ?? 0;
            $request['discount'] = $request->discount ?? 0;
            $request['created_by'] = Auth::user()->id;
            $request['department_id'] = $department_id;
            $order = Order::create($request->all());
//            logger("Order Create");
            // Order create Done

            // Order details create start
            for($i=0;$i<count($request->color_id);$i++){

                $getAllStock = Product::where('color_id',$request->color_id[$i])
                    ->where('quantity', '>', 0)->get();

                // Stock deduct from product supply start
               $quantity = $request->quantity[$i];
                $contentQty = $quantity;
                foreach ( $getAllStock as $stock ) {
                    $pro_qty = $contentQty;
                    if ( $stock->quantity < $contentQty ) {
                        $pro_qty    = $stock->quantity;
                        $contentQty = $contentQty - $stock->quantity;

                        // reduce stock quantity
                        $data['quantity'] = $stock->quantity - $pro_qty;
                        DB::table( 'products' )->where( 'id', $stock->id )->update( $data );
//                        logger( ' Updated rest qty 1' . $data['rest_quantity'] );


                        // Deduct product from transfer
                        if($stock->type == 1){
                            $product_transfer = ProductTransfer::where('id',$stock->product_transfer_id)->first();
                            $transfer_data['rest_quantity'] = $product_transfer->rest_quantity - $pro_qty;
                            DB::table( 'product_transfer' )->where( 'id', $stock->product_transfer_id )->update( $transfer_data );
                            logger('Product Transfer deduct');
                        }

                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  6;
                        $cart->color_id = $request->color_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->qty = $request->quantity[$i];
                        $cart->selling_price = $request->price[$i];
                        $cart->line_total = $request->line_total[$i];
                        $cart->save();

                    } else {

                        $contentQty                          = 0;

                        // reduce stock quantity
                        $data['quantity'] = $stock->quantity - $pro_qty;
                        DB::table( 'products' )->where( 'id', $stock->id )->update( $data );
//                        logger( ' Updated rest qty 2 ' . $data['rest_quantity'] );
//                        logger("Order details".$order->id."Material -id: ".$request->material_id[$i]."Color - ".$request->color_id[$i]."Stock ".$stock->id);

                        // Deduct product from transfer
                        if($stock->type == 1){
                            $product_transfer = ProductTransfer::where('id',$stock->product_transfer_id)->first();
                            $transfer_data['rest_quantity'] = $product_transfer->rest_quantity - $pro_qty;
                            DB::table( 'product_transfer' )->where( 'id', $stock->product_transfer_id )->update( $transfer_data );
                            logger('Product Transfer deduct');
                        }

                        $cart = new OrderDetail();
                        $cart->order_id = $order->id;
                        $cart->product_id =  6;
                        $cart->color_id = $request->color_id[$i];
                        $cart->product_transfer_id = $stock->id;
                        $cart->selling_price = $request->price[$i];
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
                $payment->releted_department_id = $department_id;
                $payment->releted_id = $order->id;
                $payment->releted_id_type = 1;
                $payment->created_by = Auth::user()->id;
                $payment->save();
                logger("Payment Updated");

//                if($request->payment_process == 'cash'){
                   $fund_info = Fund::where('department_id',$department_id)->first();
                    $fund['current_balance'] = $fund_info->current_balance + $request->paid;
                    $fund_info->update($fund);

                    $transaction = new Transaction();
                    $transaction->bank_id = $fund_info->id;
                    $transaction->source_type = 2; // 2 is account 1 is bank
                    $transaction->type = 2;
                    $transaction->date = now();
                    $transaction->payment_id = $payment->id;
                    $transaction->source_fund_id = $order->id;
                    $transaction->amount = $request->paid;
                    $transaction->reason = 'Order Payment for order id '.$order->id;
                    $transaction->created_by = Auth::user()->id;
                    $transaction->save();
//                }
            }
            // User account update end

            DB::commit();
            return ['status'=>200,'message'=>"Order Created Successful"];
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function orderInvoice($id){
        $order = Order::with('customer','details')->where('id',$id)->first();
        return view('admin.cart.include.invoice',compact('order'));
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
                $payment->releted_department_id = 6; // 6 is for knitting showroom
                $payment->releted_id_type = 1;
                $payment->created_by = Auth::user()->id;
                $payment->save();
                logger("Payment Updated");

                if($request->payment_process == 'bank'){
                    $bank_info = Bank::where('id',$request->payment_type)->first();
                    $bank['current_balance'] = $bank_info->current_balance + $request->paid;
                    $bank_info->update($bank);

                    $transaction = new Transaction();
                    $transaction->bank_id = $bank_info->id;
                    $transaction->source_type = 1;
                    $transaction->date = now();
                    $transaction->type = 2; // 1 is Widthrow 2 for deposit
                    $transaction->payment_id = $payment->id;
                    $transaction->amount = $request->paid;
                    $transaction->reason = 'Order Payment for Order ID '.$order->id;
                    $transaction->created_by = Auth::user()->id;

                    $transaction->save();

                }
                if($request->payment_process == 'account'){
                    $fund_info = Fund::where('id',$request->payment_type)->first();
                    $fund['current_balance'] = $fund_info->current_balance + $request->paid;
                    $fund_info->update($fund);

                    $transaction = new Transaction();
                    $transaction->bank_id = $fund_info->id;
                    $transaction->source_type = 2;
                    $transaction->type = 2;
                    $transaction->date = now();
                    $transaction->payment_id = $payment->id;
                    $transaction->amount = $request->paid;
                    $transaction->reason = 'Order Payment for Order ID '.$order->id;
                    $transaction->created_by = Auth::user()->id;

                    $transaction->save();

                }
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
