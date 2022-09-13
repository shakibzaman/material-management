<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSet extends Model
{
    use HasFactory;
    protected $table = 'stock_sets';
    protected $fillable = ['product_id','color_id','start_quantity','end_quantity'];

    public function color(){
        return $this->belongsTo(MaterialConfig::class,'color_id');
    }
    public function product(){
        return $this->belongsTo(MaterialConfig::class,'product_id');
    }
}
