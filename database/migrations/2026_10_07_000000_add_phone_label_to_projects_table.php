<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('phone_label')->nullable()->after('phone');
        });

        $salesIds = [30, 31, 32, 107];

        foreach (DB::table('projects')->pluck('id') as $id) {
            $label = in_array($id, $salesIds, true)
                ? ['uk' => 'Відділ продажу', 'en' => 'Sales department']
                : ['uk' => 'Контакт для звʼязку', 'en' => 'Contact for communication'];

            DB::table('projects')->where('id', $id)->update([
                'phone_label' => json_encode($label, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('phone_label');
        });
    }
};
