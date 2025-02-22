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
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\WishlistController;
use App\Models\Review;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'LogIn');
        Route::get("check-email","checkEmail");


    });

    Route::middleware('auth:sanctum')->group(function () {


        //Admin
        Route::get('admin-data',[AdminController::class,"AdminData"]);
        Route::get("admin-activity",[AdminMonitoringController::class,"adminActivity"]);
        Route::post('logout', [AuthController::class,"logout"]);
        Route::post('admin/change-password',[AdminController::class,"changePassword"]);
        Route::get("review-data/{id}",[ReviewController::class,"ratings"]);

        //Customer
        Route::post('customer/change-password',[CustomerController::class,"changePassword"]);
        Route::get('customer-data',[CustomerController::class,"getCustomerData"]);
        Route::post('order-products',[OrderController::class,"store"]);
        Route::get("available-payments",[PublicController::class,"availablePayments"]);
        Route::put("update-customer",[CustomerController::class,"update"]);
        Route::get("order-information",[CustomerController::class,"customerOrderInformation"]);
        Route::get("order-history",[CustomerController::class,"customerOrderHistory"]);
        Route::get("order-details/{id}",[CustomerController::class,"customerOrderDetails"]);

        Route::get("customer-question-history",[CustomerController::class,"getAllCustomerQuestions"]);
        Route::get("customer-answer",[CustomerController::class,"getAllCustomerAnswers"]);
        Route::post("ask-question",[CustomerController::class,"askQuestion"]);

        Route::apiResource("wishlist",WishlistController::class)->only(["store","destroy"]);
        Route::get("customer-wishlist",[CustomerController::class,"wishList"]);

        Route::post("write-review",[ReviewController::class,"store"]);
    });

    Route::middleware(['auth:sanctum', 'user-role:Product Management'])->group(function () {

        Route::apiResource('size',SizeController::class);
        Route::apiResource("color",ColorController::class);
        Route::apiResource('type',TypeController::class);


        Route::post('brand/update-image',[BrandController::class,"updatePhoto"]);
        Route::apiResource('brand',BrandController::class);

        Route::get('category',[CategoryController::class,"index"]);


        Route::prefix("product")->group(function () {

            Route::put('update-quantity/{id}',[ProductController::class,"updateProductQuantity"]);
            Route::get('get-quantity/{id}',[ProductController::class,"getProductQuantity"]);
            Route::get("trash",[ProductController::class,"productTrash"]);
            Route::get("admin-product-info/{id}",[ProductController::class,"adminProductDetails"]);
            Route::put("delete/{id}", [ProductController::class, "deleteProduct"]);
            Route::put("restore/{id}", [ProductController::class, "restoreProduct"]);
            Route::post("details-photo-update/{id}", [ProductController::class, "updateDetailsPhoto"]);
            Route::post("cover-update/{id}", [ProductController::class, "updateCoverPhoto"]);
            Route::get("details-data/{id}", [ProductController::class, "productUpdateData"]);
            Route::get("filter-data", [ProductController::class, "getAllFilterData"]);
            Route::get("properties", [ProductController::class, "getProductProperties"]);
            Route::get("promotion",[ProductController::class,"promotion"]);
            Route::put("promotion/{id}",[ProductController::class,"createPromotion"]);
            Route::put("delete-promotion/{id}",[ProductController::class,"deletePromotion"]);

        });
        Route::apiResource("product",ProductController::class)->except(["destroy"]);
        Route::apiResource("review",ReviewController::class)->only("destroy");
        Route::get("average-rating/{id}",[ReviewController::class,"averageRating"]);

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
        Route::post("admin/restart-password/{id}",[AdminController::class,"restartPassword"]);

        Route::put("ads-change",[PageController::class,"updateADS"]);
        Route::post("create-carousel",[HeroController::class,"store"]);

        Route::post('admin/change-photo',[AdminController::class,"updatePhoto"]);
        Route::apiResource('admin',AdminController::class)->except(["destroy,show"]);

    });

    Route::middleware(['auth:sanctum', 'user-role:Blog Management'])->group(function () {

        Route::apiResource('blog',BlogController::class);

    });

    Route::controller(PublicController::class)->group(function(){

        Route::get("ads","getHeaderAds");
        Route::get("carousels","getCarousels");
        Route::get("home","getHomePage");
        Route::get("filter-data/{id}","getFilterData");
        Route::get("customer-product/{id}","getProducts");
        Route::get("product-details-data/{id}","productDetailsData");
        Route::get("product-rating/{id}","productRating");
        Route::get("rating-data/{id}","ratingData");
        Route::get("search-input","searchInput");
        Route::get("search","search");
        Route::get("all-brands","allBrands");
        Route::get("brand-filter/{brand}","brandFilter");
        Route::get("brand-products/{brand}","brandProducts");
    });

});

