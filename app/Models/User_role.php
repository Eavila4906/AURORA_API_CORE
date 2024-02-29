<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_role extends Model
{
    use HasFactory;

    protected $fillable = ['user', 'rol', 'status'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    
}
