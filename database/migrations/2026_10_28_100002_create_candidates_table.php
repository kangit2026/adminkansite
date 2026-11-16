<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            // null — анкета до загальної бази, без вакансії
            $table->foreignId('vacancy_id')->nullable()->constrained('vacancies')->nullOnDelete();
            // назва вакансії на момент відгуку: вакансію можуть перейменувати
            $table->string('vacancy_title')->nullable();
            $table->string('direction', 100)->nullable();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email');
            $table->string('linkedin')->nullable();
            $table->text('message')->nullable();
            // файл на диску local (storage/app/private), назовні — лише через адмінку
            $table->string('cv_path')->nullable();
            $table->string('cv_original_name')->nullable();
            $table->unsignedInteger('cv_size')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
