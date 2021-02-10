<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Default admin user
        $admin = User::forceCreate([
            'is_admin' => true,
            'is_guest' => false,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Default team leader
        $leader = User::forceCreate([
            'is_admin' => false,
            'is_guest' => false,
            'name' => 'Team Leader',
            'email' => 'team-leader@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Default employee
        $employee = User::forceCreate([
            'is_admin' => false,
            'is_guest' => false,
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Unverified admin
        $unverifiedAdmin = User::forceCreate([
            'is_admin' => true,
            'is_guest' => false,
            'email' => 'unverified-admin@example.com',
        ]);

        // Unverified employee
        $unverifiedEmployee = User::forceCreate([
            'is_admin' => false,
            'is_guest' => false,
            'email' => 'unverified-user@example.com',
        ]);

        // Default team
        $team = Team::forceCreate([
            'name' => 'Default Team',
            'department' => 'Default Department',
        ]);

        // Attach employees to team
        $team->users()->sync([
            $admin->id,
            $employee->id,
            $unverifiedAdmin->id,
            $unverifiedEmployee->id,
        ]);

        // Attach team leader to team
        $team->users()->attach([
            $leader->id => ['is_leader' => true],
        ]);

        // Create live event
        Event::forceCreate([
            'code' => Event::generateUniqueEventCode(),
            'description' => 'An event ready for feedback',
            'name' => 'Live Event',
            'starts_at' => now(),
            'ends_at' => now(),
            'allow_guests' => true,
            'is_draft' => false,
        ]);

        // Create draft event
        Event::forceCreate([
            'code' => Event::generateUniqueEventCode(),
            'description' => 'An event with questions being prepared',
            'name' => 'Draft Event',
            'starts_at' => now(),
            'ends_at' => now(),
            'allow_guests' => true,
            'is_draft' => true,
        ]);
    }
}
