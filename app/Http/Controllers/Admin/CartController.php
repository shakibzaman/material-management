<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\MaterialIn;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function cart(){
        $materials = MaterialIn::with('material')->where('type',2)->get()->pluck('material.name','material.id')->prepend('Pleease Select','');
        $customers = Customer::get()->pluck('name','id')->prepend( trans( 'global.pleaseSelect' ), '' );
        return view('admin.cart.cart',compact('materials','customers'));
    }
}
