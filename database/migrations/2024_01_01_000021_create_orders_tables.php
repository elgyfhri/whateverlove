<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->char('province_code', 2);
            $table->string('province_name');
            $table->char('city_code', 4);
            $table->string('city_name');
            $table->char('district_code', 6);
            $table->string('district_name');
            $table->string('postal_code', 10)->nullable();
            $table->unsignedInteger('weight');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_cost')->default(0)->nullable();
            $table->string('shipping_courier', 20)->nullable();
            $table->string('shipping_service', 50)->nullable();
            $table->string('shipping_estimate', 50)->nullable();
            $table->unsignedBigInteger('total');
            $table->text('note')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->nullOnDelete();
            $table->string('product_name');
            $table->string('image')->nullable();
            $table->string('size', 10)->nullable();
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('weight');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};