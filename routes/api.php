<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/orders',[OrderController::class,'store']);

Route::get('/orders/{order}',[OrderController::class,'show']);

Route::post('/payments/initiate',[PaymentController::class,'initiate']);
Route::get('/payments/success', [PaymentController::class, 'success']);
Route::get('/payments/cancel', [PaymentController::class, 'cancel']);
