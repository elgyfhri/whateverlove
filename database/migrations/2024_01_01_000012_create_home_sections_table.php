<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('link_text')->nullable();
            $table->string('link')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('home_sections')->insert([
            [
                'key' => 'contact_us',
                'title' => 'Contact Us',
                'link_text' => 'Learn More',
                'link' => '/contact',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'our_stores',
                'title' => 'Information',
                'link_text' => 'View More',
                'link' => '/information',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'our_services',
                'title' => 'Our Services',
                'link_text' => 'View More',
                'link' => '/#about',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};