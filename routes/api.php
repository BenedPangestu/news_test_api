<?php

use App\Http\Controllers\API\BeritaController;
use App\Http\Controllers\API\KomentarController;
use App\Http\Controllers\AuthController;
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


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('unauthenticated', [AuthController::class, 'unauthenticated'])->name('unauthenticated');

Route::middleware('auth:api')->group(function () {
    Route::get('/account', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    //route berita
    Route::prefix('news')->group(function () {
        Route::get('/', [BeritaController::class, 'index']);
        Route::get('/{id}', [BeritaController::class, 'show']);
        Route::post('/insert', [BeritaController::class, 'create']);
        Route::post('/update/{id}', [BeritaController::class, 'update']);
        Route::delete('/delete/{id}', [BeritaController::class, 'destroy']);
    });

    //route komentar
    Route::prefix('komentar')->group(function () {
        Route::get('/', [KomentarController::class, 'index']);
        Route::get('/{id}', [KomentarController::class, 'show']);
        Route::post('/insert', [KomentarController::class, 'create']);
        Route::post('/update/{id}', [KomentarController::class, 'update']);
        Route::delete('/delete/{id}', [KomentarController::class, 'destroy']);
    });
});