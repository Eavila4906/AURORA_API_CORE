<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'module', 
        'path', 
        'description',
        'icon', 
        'status'
    ];
    protected $primaryKey = 'id';

    public function submodules()
    {
        return $this->hasMany(Submodule::class);
    }
}
