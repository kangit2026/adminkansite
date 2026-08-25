<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inside_community_blocks', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->string('layout')->nullable();
            $table->unsignedTinyInteger('number')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('published');

            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('stat_value')->nullable();
            $table->json('stat_label')->nullable();
            $table->json('highlight_small')->nullable();
            $table->json('highlight_big')->nullable();
            $table->json('highlight_medium')->nullable();
            $table->json('cta_text')->nullable();

            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inside_community_blocks');
    }
};
