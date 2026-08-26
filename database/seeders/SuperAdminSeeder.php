<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gpscamera.app'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'displayname' => 'Super Admin',
                'email' => 'admin@gpscamera.app',
                'password' => Hash::make('123456'),
                'admin_id' => 'ADM-0001',
                'role' => 'Super Admin',
                'department' => 'Executive',
                'city' => 'Melapalayam',
                'district' => 'Tirunelveli',
                'state' => 'Tamil Nadu',
                'country' => 'India',
                'mobilenumber' => '+91 9025567890',
                'two_factor_enabled' => true,
                'status' => User::STATUS_ACTIVE,
                'active_sessions_count' => 2,
                'lastloginat' => Carbon::parse('2026-08-19 00:42:00'),
                'created_at' => Carbon::parse('2026-01-12 09:00:00'),
                'email_verified_at' => Carbon::parse('2026-01-12 09:00:00'),
                'dob' => '2005-01-01',
                'language' => 'English',
                'timezone' => 'Asia/Kolkata',
                'address' => '100 Enterprise Way, Suite 500',
                'isblocked' => false,
                'passwordchangedat' => Carbon::now()->subDays(5),
            ]
        );
    }
}
