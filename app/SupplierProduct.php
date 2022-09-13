<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    use HasFactory;
    protected $table = 'supplier_products';
    protected $fillable = ['material_id','quantity','supplier_id','material_in_id','total_price','paid_amount','due_amount','payment_process','payment_info'];

}
