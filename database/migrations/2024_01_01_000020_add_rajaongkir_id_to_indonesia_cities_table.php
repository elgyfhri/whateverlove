<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('laravolt.indonesia.table_prefix').'cities', function (Blueprint $table) {
            $table->unsignedBigInteger('rajaongkir_id')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table(config('laravolt.indonesia.table_prefix').'cities', function (Blueprint $table) {
            $table->dropColumn('rajaongkir_id');
        });
    }
};