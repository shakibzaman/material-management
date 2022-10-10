<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;
    protected $table = 'bank_transaction';

    public function fund(){
        return $this->belongsTo(Fund::class,'source_fund_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
