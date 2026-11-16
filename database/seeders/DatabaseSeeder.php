<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(InsideCommunitySeeder::class);
        $this->call(MarketPageSeeder::class);
        $this->call(CareerPageSeeder::class);
        $this->call(ContactsPageSeeder::class);
        $this->call(CareerVacancySeeder::class);
    }
}
