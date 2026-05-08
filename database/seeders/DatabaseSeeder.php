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
        // Seed roles and permissions first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            TourPackageSeeder::class,
            SettingsSeeder::class,
        ]);

        // Create SuperAdmin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@ilm.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign SuperAdmin role
        if (!$superAdmin->hasRole('SuperAdmin')) {
            $superAdmin->assignRole('SuperAdmin');
        }

        // Create other role users for testing
        $approver = User::firstOrCreate(
            ['email' => 'approver@ilm.com'],
            [
                'name' => 'Approver User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$approver->hasRole('Approver')) {
            $approver->assignRole('Approver');
        }

        $inspector = User::firstOrCreate(
            ['email' => 'inspector@ilm.com'],
            [
                'name' => 'Inspector User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$inspector->hasRole('Inspector')) {
            $inspector->assignRole('Inspector');
        }
    }
}
