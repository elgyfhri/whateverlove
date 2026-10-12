<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shopee_url', 'tokopedia_url', 'bukalapak_url']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('shopee_url')->nullable()->after('image');
            $table->string('tokopedia_url')->nullable()->after('shopee_url');
            $table->string('bukalapak_url')->nullable()->after('tokopedia_url');
        });
    }
};