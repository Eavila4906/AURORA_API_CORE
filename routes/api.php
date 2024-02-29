<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\User_roleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::get('/notauthenticated', function () {
    return response()->json(['message' => 'User not authenticated'], 401);
})->name('notauthenticated');

Route::middleware('auth:api')->group(function () {
    /**
     * Logout path
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /** 
     * Authenticated user paths
     */
    Route::get('/authenticated/user', [AuthController::class, 'show']);

    /** 
     * Users paths
     */
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user/create', [UserController::class, 'store']);
    Route::put('/user/update/{id}', [UserController::class, 'update']);
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);

    /** 
     * Modules paths
     */
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/module/{id}', [ModuleController::class, 'show']);
    Route::post('/module/create', [ModuleController::class, 'store']);
    Route::put('/module/update/{id}', [ModuleController::class, 'update']);
    Route::delete('/module/delete/{id}', [ModuleController::class, 'destroy']);

    /** 
     * Roles paths
     */
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role/create', [RoleController::class, 'store']);
    Route::put('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

    /**
     * Permissions paths
     */
    Route::post('/permissions/assign', [PermissionController::class, 'store']);
    Route::get('/permissions/role/{id}', [PermissionController::class, 'showByRole']);
    Route::get('/permissions/module/{mId}/role/{rId}', [PermissionController::class, 'showByModule']);
    Route::get('/permissions/modules/{id}', [PermissionController::class, 'showModulesByRole']);

    /**
     * User roles paths
     */
    Route::post('/user_roles/assign', [User_roleController::class, 'store']);
    Route::get('/user_roles/role/{id}', [User_roleController::class, 'show']);
});

//Route::post('/user/create', [UserController::class, 'store']);
