<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodule extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
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

    public function aurora_app()
    {
        return $this->belongsTo(Aurora_app::class, 'app_id', 'id');
    }
}
