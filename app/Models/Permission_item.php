<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission_item extends Model
{
    protected $table = 'permissions_items';
    protected $fillable = ['rol', 'item', 'r', 'w', 'u', 'd'];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function rol()
    {
        return $this->belongsTo('App\Models\Rol', 'rol');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item');
    }

    public static function itemPermissions($id) 
    {
        $query = Permission_item::select('permissions_items.rol', 'permissions_items.item', 
        'items.item AS nameItem', 
        'permissions_items.r', 
        'permissions_items.w', 
        'permissions_items.u', 
        'permissions_items.d'
        )
        ->join('items', 'permissions_items.item', '=', 'items.id')
        ->where('permissions_items.rol', $id)
        ->get();

        $reqPermissionsItems = array();
        for ($i=0; $i < count($query); $i++) { 
            $reqPermissionsItems[$query[$i]['item']] = $query[$i];
        }

        return $reqPermissionsItems;
    }
}
