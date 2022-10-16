<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';
    protected $fillable = ['amount','payment_process','payment_info','user_account_id','created_by','releted_id','releted_id_type','releted_department_id','date'];

    public function transaction(){
        return $this->belongsTo(Transaction::class,'id','payment_id');
}

}
