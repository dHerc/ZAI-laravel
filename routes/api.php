<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'events'], function() {
    Route::get('', [EventController::class, 'list']);
    Route::get('{event}', [EventController::class, 'get']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('', [EventController::class, 'store']);
        Route::patch('{event}', [EventController::class, 'update']);
        Route::delete('{event}', [EventController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('', [CategoryController::class, 'list']);
    Route::get('{category}', [CategoryController::class, 'get']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('', [CategoryController::class, 'store']);
        Route::patch('{category}', [CategoryController::class, 'update']);
        Route::delete('{category}', [CategoryController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'profile'], function () {
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('change_password', [PasswordController::class, 'update']);
        Route::get('token', [ProfileController::class, 'token'])->middleware();
    });
});
