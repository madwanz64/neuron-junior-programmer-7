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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
    Route::get('{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
    Route::post('', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
    Route::put('{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
});
