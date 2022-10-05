<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;
    protected $table= 'product_return';
    protected $fillable = ['product_transfer_id','quantity','return_by'];

    public function transfer(){
        return $this->belongsTo(Transfer::class,'product_transfer_id','id');
    }
    public function product(){
        return $this->belongsTo(MaterialConfig::class);
    }
}
