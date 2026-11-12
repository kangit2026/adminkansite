<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Одна таблиця заявок для всіх форм сайту: contact (зворотний зв’язок), land (пропозиція ділянки),
// apartment (заявка на квартиру/проєкт), deck (презентація на email). Поля, яких форма не має, — порожні.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->string('type', 30)->default('contact')->after('id')->index();
            $table->json('payload')->nullable()->after('message');
            $table->json('attachments')->nullable()->after('payload');
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'payload', 'attachments']);
        });
    }
};
