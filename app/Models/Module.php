<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'app_id',
        'level',
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

    public function aurora_app()
    {
        return $this->belongsTo(Aurora_app::class, 'app_id', 'id');
    }
}
