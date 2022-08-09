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
        'name',
        'quantity',
        'unit',
        'nid',
        'buying_date',
        'unit_price',
        'total_price',
        'supplied_by',
        'inv_number',
        'created_at',
        'created_by',
        'purchased_by',
        'rest'
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
}
