<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'suppliers';
    protected $fillable = ['name'];

    public function account()
    {
        return $this->belongsTo(UserAccount::class,'id','user_id')->where('type',1);
    }
}
