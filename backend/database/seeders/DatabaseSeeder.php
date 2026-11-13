<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Fills the local database with demo data. Roles are already inserted by the users migration.
    public function run(): void
    {
        $this->call(LookupSeeder::class);

        User::factory()->admin()->create([
            'username' => 'admin',
            'email' => 'admin@quasario.test',
        ]);

        User::factory()->create([
            'username' => 'demo_user',
            'email' => 'demo@quasario.test',
        ]);
    }
}
