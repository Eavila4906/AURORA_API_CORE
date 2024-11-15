<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'username',
        'email',
        'password',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function userRoles($user) 
    {
        return User_role::select('roles.id', 'roles.rol', 'aurora_apps.app')
        ->join('roles', 'user_roles.rol', '=', 'roles.id')
        ->join('aurora_apps', 'roles.app_id', '=', 'aurora_apps.id')
        ->where('user_roles.user', $user->id)
        ->where('user_roles.status', 1)
        ->get();
    }

    public static function userCompanies($user) 
    {
        return User_company::select('companies.id', 'companies.name')
        ->join('companies', 'user_companies.company', '=', 'companies.id')
        ->where('user_companies.user', $user->id)
        ->where('user_companies.status', 1)
        ->get();
    }
}
