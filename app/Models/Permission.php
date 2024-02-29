<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['rol', 'module', 'r', 'w', 'u', 'd'];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function rol()
    {
        return $this->belongsTo('App\Models\Rol', 'rol');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module');
    }

    public static function modulesPermissions($id) 
    {
        $query = Permission::select('permissions.rol', 'permissions.module', 'modules.module AS nameModule', 'permissions.r', 'permissions.w', 'permissions.u', 'permissions.d')
        ->join('modules', 'permissions.module', '=', 'modules.id')
        ->where('permissions.rol', $id)
        ->get();

        $reqPermissions = array();
        for ($i=0; $i < count($query); $i++) { 
            $reqPermissions[$query[$i]['module']] = $query[$i];
        }

        return $reqPermissions;
    }
}