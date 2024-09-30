<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'submodule_id', 
        'item', 
        'path', 
        'description',
        'icon', 
        'status'
    ];

    public function submodule()
    {
        return $this->belongsTo(Submodule::class);
    }

    public function aurora_app()
    {
        return $this->belongsTo(Aurora_app::class, 'app_id', 'id');
    }
}
