<?php

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
// register
Route::post('register', [App\Http\Controllers\API\AUTH\AuthController::class, 'register']);
// login
Route::post('login', [App\Http\Controllers\API\AUTH\AuthController::class, 'login']);

// Send password reset email
Route::post('sendResetLinkEmail', [App\Http\Controllers\API\AUTH\AuthController::class, 'sendResetLinkEmail']);

// Handle password reset
Route::post('password/reset', [App\Http\Controllers\API\AUTH\AuthController::class, 'reset'])->name('password-reset');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::post('/email/verify', [App\Http\Controllers\VerificationController::class, 'show'])->name('verify-email');
    });
    Route::group(['middleware' => ['verified']], function () {
        Route::post('logout', [App\Http\Controllers\API\AUTH\AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
