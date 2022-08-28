<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialTransfer extends Model
{
    use HasFactory;
    protected $table ='material_transfer';
    protected $fillable = ['material_id','department_id','quantity','material_stock_id'];

    public function material(){
        return $this->belongsTo(MaterialConfig::class,'material_id');
    }
    public function detail(){
        return $this->belongsTo(MaterialIn::class,'material_stock_id');
    }
    public function transfer(){
        return $this->belongsTo(Transfer::class,'transfer_id');
    }
}
