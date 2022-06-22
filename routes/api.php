<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DepartmentController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', fn() => 'works!');
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/verify-account', [UserController::class, 'verifyAccount']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::prefix('user')->group(function() {
        Route::put('{id}', [UserController::class, 'update']);
        Route::put('/profile-picture/{id}', [UserController::class, 'profilePicture']);
        Route::put('/able-to-login/{id}', [UserController::class, 'ableToLogin']);
        Route::delete('{id}', [UserController::class, 'destroy']);
    });

    // Appointment Routes
    Route::prefix('appointment')->group(function() {});

    // Card Routes
    Route::resource('card', CardController::class);

    // Department Routes
    Route::resource('department', DepartmentController::class);
});
