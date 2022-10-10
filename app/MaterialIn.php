<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\MultiTenantModelTrait;


class MaterialIn extends Model
{
    use MultiTenantModelTrait,SoftDeletes;

    public $table = 'material_ins';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'material_id',
        'quantity',
        'unit',
        'nid',
        'buying_date',
        'unit_price',
        'total_price',
        'supplier_id',
        'inv_number',
        'created_at',
        'created_by',
        'purchased_by',
        'rest',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,'purchased_by');
    }
    public function units()
    {
        return $this->belongsTo(Unit::class,'unit');
    }
    public function material()
    {
        return $this->belongsTo(MaterialConfig::class,'material_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    public function supplierProduct()
    {
        return $this->hasMany(SupplierProduct::class,'material_in_id');
    }

}
