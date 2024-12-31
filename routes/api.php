<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMonitoringController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerQuestionController;
use App\Http\Controllers\DeliverController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });

    Route::middleware(['auth:sanctum', 'user-role:Product Admin'])->group(function () {
        Route::apiResource('size',SizeController::class);
        Route::apiResource("color",ColorController::class);
        Route::apiResource('type',TypeController::class);

        Route::post('brand/update-image',[BrandController::class,"updatePhoto"]);
        Route::apiResource('brand',BrandController::class);

        Route::get('category',[CategoryController::class,"index"]);

        Route::get("filter-data",[ProductController::class,"getAllFilterData"]);
        Route::get("product-properties/{id}",[ProductController::class,"getProductProperties"]);
        Route::apiResource("product",ProductController::class)->except(["destroy"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Customer Support'])->group(function () {

        Route::controller(CustomerQuestionController::class)->group(function(){
            Route::get("questions","getAllQuestions");
            Route::get("answers","getAllAnswers");
            Route::put("answer-question","answerQuestion");

        });
    });

    Route::middleware(['auth:sanctum', 'user-role:Order Management'])->group(function () {

        Route::apiResource("deliver",DeliverController::class)->only(["index"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Super Admin'])->group(function () {

        Route::apiResource("deliver",DeliverController::class);
        Route::apiResource("payment",PaymentController::class);

    });



});











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






Route::apiResource('category',CategoryController::class) -> only(["index"]);


// Category



// Type

// Brand




// Admin Activity
Route::apiResource('activity',AdminMonitoringController::class)->only(["index"]);

// Blog
Route::post("blog/update-image",[BlogController::class,"updatePhoto"]);
Route::apiResource("blog",BlogController::class);

// Product
Route::post("product/update-cover",[ProductController::class,"updatePhoto"]);
Route::put("product/restore",[ProductController::class,"restoreProduct"]);
Route::put("product/delete",[ProductController::class,"deleteProduct"]);


// Product Sizes
Route::apiResource("product-size",ProductSizeController::class)->only(["store","update"]);

// Product Payment

// Delivery

// Order
Route::apiResource("order",OrderController::class)->only(["store","update"]);

// Order Details
Route::apiResource("order-details",OrderDetailsController::class)->only(["store"]);

// Review
Route::apiResource("review",ReviewController::class)->only(["store","destroy"]);

// WishList
Route::apiResource("wishlist",WishlistController::class)->only(["store","destroy"]);
