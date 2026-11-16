<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('career_page', function (Blueprint $table) {
            // адреси через кому: лист про кожен новий відгук
            $table->string('notify_emails', 500)->nullable()->after('og_image');
            // порожньо — блок «Знаєте сильного спеціаліста?» на сайті не показується
            $table->string('referral_url', 500)->nullable()->after('notify_emails');
        });
    }

    public function down(): void
    {
        Schema::table('career_page', function (Blueprint $table) {
            $table->dropColumn(['notify_emails', 'referral_url']);
        });
    }
};
