<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerQuestionController;
use App\Http\Controllers\UserController;
use App\Models\CustomerQuestion;
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
