<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMonitoringController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerQuestionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::put('user/change-password',[UserController::class,"updatePassword"]);

// Customer
Route::apiResource('customer',CustomerController::class) -> only(["show","store","update","index"]);

// Admin
Route::post('admin/change-photo',[AdminController::class,"updatePhoto"]);
Route::apiResource('admin',AdminController::class)->only(["show","store","update","index"]);

// Custom Question
Route::post('ask-question',[CustomerQuestionController::class,"askQuestion"]);
Route::post('answer-question',[CustomerQuestionController::class,"answerQuestion"]);
Route::get('questions',[CustomerQuestionController::class,"getAllQuestions"]);
Route::get('answers',[CustomerQuestionController::class,"getAllAnswers"]);
Route::get('questions/{id}',[CustomerQuestionController::class,"getAllCustomerQuestions"]);
Route::get('answer/{id}',[CustomerQuestionController::class,"getAllCustomerAnswers"]);
Route::delete('questions/{id}',[CustomerQuestionController::class,"destroy"]);

// Category
Route::apiResource('category',CategoryController::class) -> only(["index"]);

// Size
Route::apiResource('size',SizeController::class);

// Type
Route::apiResource('type',TypeController::class);

// Brand
Route::post('brand/update-image',[BrandController::class,"updatePhoto"]);
Route::apiResource('brand',BrandController::class);

// Color
Route::apiResource("color",ColorController::class);

// Admin Activity
Route::apiResource('activity',AdminMonitoringController::class)->only(["index"]);

// Blog
Route::post("blog/update-image",[BlogController::class,"updatePhoto"]);
Route::apiResource("blog",BlogController::class);

// Product
Route::post("product/update-cover",[ProductController::class,"updatePhoto"]);
Route::put("product/restore",[ProductController::class,"restoreProduct"]);
Route::put("product/delete",[ProductController::class,"deleteProduct"]);
Route::apiResource("product",ProductController::class)->except(["destroy"]);
