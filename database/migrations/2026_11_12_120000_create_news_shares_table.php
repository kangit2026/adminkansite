<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Натискання «Поділитися» на сторінці новини: одна строка — одне натискання.
 * Лише новина, канал і час — без IP, браузера чи інших персональних даних.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('channel', 16);
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_shares');
    }
};
