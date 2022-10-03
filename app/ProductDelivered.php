<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDelivered extends Model
{
    use HasFactory;

    protected $table = 'product_delivered';

    public function product()
    {
        return $this->belongsTo(MaterialConfig::class,'product_id');
    }
}