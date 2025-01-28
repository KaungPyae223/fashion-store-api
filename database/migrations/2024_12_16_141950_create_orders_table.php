<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("customer_id");
            $table->unsignedBigInteger("payment_id");
            $table->unsignedBigInteger("delivery_id")->nullable();
            $table->unsignedBigInteger("admin_id")->nullable();
            $table->integer("total_products");
            $table->integer("sub_total");
            $table->integer("tax");
            $table->integer("discount_amount")->default(0);
            $table->integer("profit_amount")->default(0);
            $table->integer("total_qty");
            $table->integer("total_price");
            $table->string("name");
            $table->string("email");
            $table->string("phone");
            $table->longText("address");
            $table->longText("note")->nullable();
            $table->enum("status",["prepare","delivered"])->default("prepare");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
