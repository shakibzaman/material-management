<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table ='order_details';

    public function product(){
        return $this->belongsTo(MaterialConfig::class);
    }
    public function color(){
        return $this->belongsTo(MaterialConfig::class);
    }
    public function order(){
        return $this->hasOne(Order::class,'id','order_id');
    }
}
