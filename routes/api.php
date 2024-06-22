<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SubmoduleController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Permission_submoduleController;
use App\Http\Controllers\Permission_itemController;
use App\Http\Controllers\User_roleController;
use App\Http\Controllers\MenuController;

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
     * Validate auth path
     */
    Route::get('/validateToken', function () {
        return response()->json(['message' => 'Token valid', 'status' => 'ok'], 200);
    });

    /**
     * Logout path
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /** 
     * Authenticated user paths
     */
    Route::get('/authenticated/user', [AuthController::class, 'show']);

    /**
     * Menu path
     */
    Route::post('/menu', [MenuController::class, 'index']);

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
     * Submodules paths
     */
    Route::get('/submodules', [SubmoduleController::class, 'index']);
    Route::get('/submodule/{id}', [SubmoduleController::class, 'show']);
    Route::post('/submodule/create', [SubmoduleController::class, 'store']);
    Route::put('/submodule/update/{id}', [SubmoduleController::class, 'update']);
    Route::delete('/submodule/delete/{id}', [SubmoduleController::class, 'destroy']);

    /** 
     * Items paths
     */
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/item/{id}', [ItemController::class, 'show']);
    Route::post('/item/create', [ItemController::class, 'store']);
    Route::put('/item/update/{id}', [ItemController::class, 'update']);
    Route::delete('/item/delete/{id}', [ItemController::class, 'destroy']);

    /** 
     * Roles paths
     */
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role/create', [RoleController::class, 'store']);
    Route::put('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

    /**
     * Permissions modules paths
     */
    Route::post('/permissions/assign', [PermissionController::class, 'store']);
    Route::get('/permissions/role/{id}', [PermissionController::class, 'showByRole']);
    Route::get('/permissions/module/{mId}/role/{rId}', [PermissionController::class, 'showByModule']);
    Route::get('/permissions/modules/{id}', [PermissionController::class, 'showModulesByRole']);

    /**
     * Permissions submodules paths
     */
    Route::post('/permissions_submodule/assign', [Permission_submoduleController::class, 'store']);
    Route::get('/permissions_submodule/role/{id}', [Permission_submoduleController::class, 'showByRole']);
    Route::get('/permissions_submodule/submodule/{smId}/role/{rId}', [Permission_submoduleController::class, 'showBySubmodule']);
    Route::get('/permissions_submodule/submodules/{id}', [Permission_submoduleController::class, 'showSubmodulesByRole']);

    /**
     * Permissions items paths
     */
    Route::post('/permissions_item/assign', [Permission_itemController::class, 'store']);
    Route::get('/permissions_item/role/{id}', [Permission_itemController::class, 'showByRole']);
    Route::get('/permissions_item/item/{itemId}/role/{rId}', [Permission_itemController::class, 'showByItem']);
    Route::get('/permissions_item/items/{id}', [Permission_itemController::class, 'showItemsByRole']);

    /**
     * User roles paths
     */
    Route::post('/user_roles/assign', [User_roleController::class, 'store']);
    Route::get('/user_roles/role/{id}', [User_roleController::class, 'show']);
});
