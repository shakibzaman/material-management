<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable =['invoice_id','customer_id','total','sub_total','discount','paid','due','date','payment_process','payment_info','created_by','department_id'];

    public function details(){
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function showroom(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
}
