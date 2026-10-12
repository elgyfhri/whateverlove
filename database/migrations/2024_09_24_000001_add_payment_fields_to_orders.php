<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('pending')->nullable()->after('status');
            $table->string('payment_reference', 100)->nullable()->after('payment_status');
            $table->string('payment_redirect_url', 500)->nullable()->after('payment_reference');
            $table->text('payment_raw')->nullable()->after('payment_redirect_url');
            $table->timestamp('paid_at')->nullable()->after('payment_raw');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_reference', 'payment_redirect_url', 'payment_raw', 'paid_at']);
        });
    }
};