<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = ['name'];

    public function account()
    {
        return $this->belongsTo(UserAccount::class,'id','user_id')->where('type',2);
    }
}
