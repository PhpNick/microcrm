<?php

use App\Http\Controllers\MovementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('warehouses', [WarehouseController::class, 'index']);
Route::get('products/{product}/stocks', [ProductController::class, 'stocks']);
Route::get('orders', [OrderController::class, 'index']);
Route::post('orders', [OrderController::class, 'store']);
Route::put('orders/{order}', [OrderController::class, 'update']);
Route::post('orders/{order}/complete', [OrderController::class, 'complete']);
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
Route::post('orders/{order}/resume', [OrderController::class, 'resume']);
Route::get('movements', [MovementController::class, 'index']);

