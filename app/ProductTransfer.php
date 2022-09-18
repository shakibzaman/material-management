<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    use HasFactory;
    protected $table = 'product_transfer';
    protected $fillable = ['product_id','quantity','rest_quantity','company_id','department_id','created_by','transfer_id','product_stock_id','process_fee'];

    public function product(){
        return $this->belongsTo(MaterialConfig::class,'product_id');
    }
    public function detail(){
        return $this->belongsTo(MaterialIn::class,'product_stock_id');
    }
    public function color(){
        return $this->belongsTo(MaterialConfig::class,'color_id');
    }

    public function product_transfer_detail(){
        return $this->belongsTo(ProductTransfer::class,'product_stock_id');
    }
    public function transfer(){
        return $this->belongsTo(Transfer::class,'transfer_id');
    }
    public function expense(){
        return $this->belongsTo(Expense::class,'transfer_product_id');
    }

}
