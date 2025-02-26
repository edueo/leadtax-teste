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
            $table->string('mercadolivre_id')->unique();
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('available_quantity')->default(0);
            $table->integer('sold_quantity')->default(0);
            $table->string('condition')->nullable();
            $table->text('permalink')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('seller_id')->nullable();
            $table->string('seller_name')->nullable();
            $table->float('seller_rating')->nullable();
            $table->string('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->json('shipping_info')->nullable();
            $table->timestamp('last_updated')->nullable();
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
