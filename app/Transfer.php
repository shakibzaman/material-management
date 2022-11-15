<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;
    protected $table = 'transfer';
    protected $fillable = ['company_id','department_id','created_by','date'];

    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }
    public function transfer(){
        return $this->hasMany(ProductTransfer::class,'transfer_id','id');
    }
    public function material(){
        return $this->hasMany(MaterialTransfer::class,'transfer_id','id');
    }
}
