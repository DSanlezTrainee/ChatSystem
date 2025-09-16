<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = \App\Models\User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Admin User',
            'permission' => 'admin',
            'status' => 'active',
            'password' => bcrypt('test1234'),
        ]);

        // Ensure 'All Talk' room exists with admin as owner
        $admin = \App\Models\User::where('permission', 'admin')->first();
        if ($admin) {
            \App\Models\Room::firstOrCreate([
                'name' => 'All Talk',
            ], [
                'avatar' => null,
                'owner_id' => $admin->id,
            ]);
        }
    }
}
