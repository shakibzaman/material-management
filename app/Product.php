<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public function color(){
        return $this->belongsTo(MaterialConfig::class,'color_id','id');
    }
}
