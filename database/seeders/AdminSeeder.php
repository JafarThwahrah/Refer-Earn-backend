<?php

namespace Database\Seeders;

use App\Models\ReferralLink;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //seeder for the admin
        $admin = User::updateOrCreate([
            'name' => 'admin',
            'phone' => '0785351933',
            'email' => "admin@admin.com",
            'password' => "admin@123",
            'is_admin' => true,
        ]);

        Wallet::updateOrCreate([
            'user_id' => $admin->id,

        ]);

        ReferralLink::updateOrCreate([
            'user_id' => $admin->id,

        ]);
    }
}
