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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path'); // напр. /news/stara-nazva
            $table->string('to_path');   // напр. /news/nova-nazva
            $table->string('locale', 5);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->timestamps();

            $table->unique(['from_path', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
