<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;
    protected $table = 'user_account';
    protected $fillable = ['user_id','type','opening_balance','total_due','total_paid','created_by'];
}
