<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSetMaterial extends Model
{
    use HasFactory;
    protected $table = 'stock_set_materials';
    protected $fillable = ['stock_set_id','material_id','material_quantity'];

    public function material(){
        return $this->belongsTo(MaterialConfig::class,'material_id');
    }
}
