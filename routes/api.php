<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('user/change-password',[UserController::class,"updatePassword"]);
Route::put('user/change-name',[UserController::class,"updateName"]);
Route::apiResource('user',UserController::class);
