<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission_submodule extends Model
{
    protected $table = 'permissions_submodules';
    protected $fillable = ['rol', 'submodule', 'r', 'w', 'u', 'd'];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function rol()
    {
        return $this->belongsTo('App\Models\Rol', 'rol');
    }

    public function submodule()
    {
        return $this->belongsTo('App\Models\Submodule', 'submodule');
    }

    public static function submodulesPermissions($id) 
    {
        $query = Permission_submodule::select('permissions_submodules.rol', 'permissions_submodules.submodule', 
        'submodules.submodule AS nameSubmodule', 
        'permissions_submodules.r', 
        'permissions_submodules.w', 
        'permissions_submodules.u', 
        'permissions_submodules.d'
        )
        ->join('submodules', 'permissions_submodules.submodule', '=', 'submodules.id')
        ->where('permissions_submodules.rol', $id)
        ->get();

        $reqPermissionsSubmodules = array();
        for ($i=0; $i < count($query); $i++) { 
            $reqPermissionsSubmodules[$query[$i]['submodule']] = $query[$i];
        }

        return $reqPermissionsSubmodules;
    }
}
