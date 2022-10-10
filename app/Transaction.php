<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function fund(){
        return $this->belongsTo(Fund::class,'source_fund_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function payment(){
        return $this->belongsTo(Payment::class,'payment_id');
    }
}
