<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            // після першої публікації не змінюється — інакше ламаються поширені посилання
            $table->string('slug')->unique();
            $table->foreignId('direction_id')->constrained('career_directions')->restrictOnDelete();
            $table->string('project')->nullable();
            $table->string('employment_type', 20);
            $table->string('place')->nullable();
            $table->text('summary');
            $table->text('about');
            // списки: ["пункт 1", "пункт 2", ...]
            $table->json('tasks');
            $table->json('requirements')->nullable();
            $table->json('nice_to_have')->nullable();
            $table->json('offer')->nullable();
            $table->string('status', 10)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
