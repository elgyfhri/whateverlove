<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropUnique('cart_items_cart_id_product_id_unique');

            $table->string('size')->nullable()->after('product_id');

            $table->unique(['cart_id', 'product_id', 'size'], 'cart_items_cart_product_size_unique');
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropUnique('cart_items_cart_product_size_unique');

            $table->dropColumn('size');

            $table->unique(['cart_id', 'product_id']);
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
        });
    }
};