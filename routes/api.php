<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//CREATING ROUTES
//creating the API for the todo app using Laravel passport

Route::prefix('user')->group(function () {
    // no need any authorization for login and register since we used prefix
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);

      
    // passport auth api
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/', [UserController::class, 'user']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::put('update', [UserController::class, 'update']);
        Route::put('updateImage', [UserController::class, 'updateImage']);
    // todos resource route
        Route::resource('todos', TodoController::class);
      
    });

});