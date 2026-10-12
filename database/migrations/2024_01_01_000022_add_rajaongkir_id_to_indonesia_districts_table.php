<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indonesia_districts', function (Blueprint $table) {
            $table->unsignedBigInteger('rajaongkir_id')->nullable()->after('meta');
            $table->index('rajaongkir_id');
        });
    }

    public function down(): void
    {
        Schema::table('indonesia_districts', function (Blueprint $table) {
            $table->dropIndex(['rajaongkir_id']);
            $table->dropColumn('rajaongkir_id');
        });
    }
};