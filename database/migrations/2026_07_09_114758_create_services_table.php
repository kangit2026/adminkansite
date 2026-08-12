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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            // Внутрішній шлях (/platform) або зовнішнє посилання (https://...)
            // — на фронті за цим і визначається, яку іконку показати
            // (стрілка/глобус), окремого поля для іконки не потрібно.
            $table->string('link');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('published'); // draft|published
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
