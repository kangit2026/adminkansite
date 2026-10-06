<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts_page', function (Blueprint $table) {
            $table->id();

            $table->json('heading')->nullable();
            $table->json('name_label')->nullable();
            $table->json('phone_label')->nullable();
            $table->json('email_label')->nullable();
            $table->json('message_label')->nullable();
            $table->json('send_label')->nullable();
            $table->json('success_message')->nullable();
            $table->json('error_message')->nullable();
            $table->json('send_again_label')->nullable();

            $table->boolean('map_enabled')->default(false);
            $table->decimal('map_lat', 11, 8)->nullable();
            $table->decimal('map_lng', 11, 8)->nullable();
            $table->unsignedTinyInteger('map_zoom')->default(16);

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts_page');
    }
};
