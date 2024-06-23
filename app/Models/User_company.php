<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_company extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'company', 'status'];
    protected $primaryKey = 'id';
    public $timestamps = false;
}
