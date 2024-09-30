<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'rol', 
        'description', 
        'status'
    ];
    protected $primaryKey = 'id';

    public function aurora_app()
    {
        return $this->belongsTo(Aurora_app::class, 'app_id', 'id');
    }
}
