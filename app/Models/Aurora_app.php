<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aurora_app extends Model
{
    use HasFactory;

    protected $fillable = [
        'app', 
        'description',
        'status'
    ];
    protected $primaryKey = 'id';

    public function roles()
    {
        return $this->hasMany(Role::class, 'app_id', 'id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'app_id', 'id');
    }

    public function submodules()
    {
        return $this->hasMany(Submodule::class, 'app_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'app_id', 'id');
    }

}
