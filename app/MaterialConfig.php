<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialConfig extends Model
{
    use HasFactory;
    public $table = 'material_configs';

    protected $fillable = ['name','type'];
    protected $dates = ['created_at','updated_at','deleted_at'];
}
