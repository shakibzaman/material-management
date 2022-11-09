<?php

namespace App;

use App\Traits\MultiTenantModelTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Employee extends Model
{
    use MultiTenantModelTrait,SoftDeletes;
    public $table = 'employees';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'name',
        'phone',
        'address',
        'department_id',
        'salary',
        'joining_date',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
    ];
    public function department(){
        return $this->belongsTo(Department::class,'department_id');
    }
}
