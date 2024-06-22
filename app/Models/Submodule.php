<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodule extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'submodule', 
        'path', 
        'description', 
        'icon',
        'status'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
