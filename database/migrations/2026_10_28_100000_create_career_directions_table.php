<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Напрями вакансій (фільтр на /career). Лише українською — вакансії
     * англійської версії не мають.
     */
    public function up(): void
    {
        Schema::create('career_directions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_directions');
    }
};
