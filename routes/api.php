<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\PinjamanController;
use App\Http\Controllers\Api\BarangController;
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
Route::post('login', [AuthController::class, 'login']);
Route::post('customer/login', [AuthController::class, 'login_customer']);
Route::post('cek-login', [AuthController::class, 'cek_login']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
Route::middleware('auth:sanctum')->group( function () {
    Route::group(['prefix' => 'master'],function(){
        Route::get('nilai', [MasterController::class, 'nilai']);
        Route::get('tujuan', [MasterController::class, 'tujuan']);
    });
    Route::group(['prefix' => 'pinjaman'],function(){
        Route::post('/', [PinjamanController::class, 'store']);
    });
});

Route::get('barang', [BarangController::class, 'barang']);
Route::post('register', [AuthController::class, 'register']);
