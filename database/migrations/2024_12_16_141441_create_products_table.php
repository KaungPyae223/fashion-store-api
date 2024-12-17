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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("type_id");
            $table->unsignedBigInteger("brand_id");
            $table->unsignedBigInteger("category_id");
            $table->unsignedBigInteger("color_id");
            $table->string("name");
            $table->longText("cover_photo");
            $table->integer("price");
            $table->longText("description");
            $table->string("status")->default("public");
            $table->enum("gender",["Men","Women","All"])->default("All");
            $table->boolean("is_delete")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
