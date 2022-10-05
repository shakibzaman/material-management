<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable =['name','phone','address','type'];

    public function account(){
        return $this->belongsTo(UserAccount::class,'id','user_id')->where('type',3);
    }
}
