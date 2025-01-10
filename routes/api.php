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
use App\Http\Controllers\HeroController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\PublicController;
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

    Route::middleware(['auth:sanctum', 'user-role:Product Management'])->group(function () {

        Route::apiResource('size',SizeController::class);
        Route::apiResource("color",ColorController::class);
        Route::apiResource('type',TypeController::class);

        Route::post('brand/update-image',[BrandController::class,"updatePhoto"]);
        Route::apiResource('brand',BrandController::class);

        Route::get('category',[CategoryController::class,"index"]);


        Route::prefix("product")->group(function () {

            Route::get("trash",[ProductController::class,"productTrash"]);
            Route::get("admin-product-info/{id}",[ProductController::class,"adminProductDetails"]);
            Route::put("delete/{id}", [ProductController::class, "deleteProduct"]);
            Route::put("restore/{id}", [ProductController::class, "restoreProduct"]);
            Route::post("details-photo-update/{id}", [ProductController::class, "updateDetailsPhoto"]);
            Route::post("cover-update/{id}", [ProductController::class, "updateCoverPhoto"]);
            Route::get("details-data/{id}", [ProductController::class, "productUpdateData"]);
            Route::get("filter-data", [ProductController::class, "getAllFilterData"]);
            Route::get("properties/{id}", [ProductController::class, "getProductProperties"]);

        });
        Route::apiResource("product",ProductController::class)->except(["destroy"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Customer Support'])->group(function () {

        Route::controller(CustomerQuestionController::class)->group(function(){
            Route::get("questions","getAllQuestions");
            Route::get("answers","getAllAnswers");
            Route::put("answer-question","answerQuestion");

        });

        Route::get("customer-order",[OrderController::class,"customerOrder"]);
        Route::get("order/order-history/{id}",[OrderController::class,"orderHistoryDetails"]);

        Route::get("customer-list",[CustomerController::class,"index"]);
        Route::get("customer-details/{id}",[CustomerController::class,"customerDetails"]);

        Route::get('all-payment',[PaymentController::class,"allPayments"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Order Management'])->group(function () {

        Route::controller(OrderController::class)->group(function () {
            Route::get('order/order-history/{id}', 'orderHistoryDetails');
            Route::get('delivery', 'deliverData');
            Route::get('order/order-history', 'orderHistory');
            Route::get('order/package/{id}', 'packagingData');
            Route::put('order/change-status/{id}', 'update');
            Route::get('order-list', 'index');
            Route::get('order-history', 'orderHistory');
        });
        Route::apiResource("deliver",DeliverController::class)->only(["index"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Super Admin'])->group(function () {

        Route::apiResource("deliver",DeliverController::class);
        Route::apiResource("payment",PaymentController::class);

    });

    Route::middleware(['auth:sanctum', 'user-role:System Admin'])->group(function () {

        Route::get("admin-monitoring",[AdminMonitoringController::class,"index"]);

        Route::put("ads-change",[PageController::class,"updateADS"]);
        Route::post("create-carousel",[HeroController::class,"store"]);

        Route::post('admin/change-photo',[AdminController::class,"updatePhoto"]);
        Route::apiResource('admin',AdminController::class)->except(["destroy"]);

    });

    Route::get("ads",[PublicController::class,"getHeaderAds"]);
    Route::get("carousels",[PublicController::class,"getCarousels"]);

});

