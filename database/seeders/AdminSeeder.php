<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = Admin::updateOrCreate(['id' => 1],  [
            'name' => 'admin1',
            'email' => 'admin@open-border.com',
            'email_verified_at' => \Carbon\Carbon::now(),
            'password' => bcrypt('vUN7E@!2v5tJSyZ'),
        ]);
        $adminUser = Admin::updateOrCreate(['id' => 2],  [
            'name' => 'admin2',
            'email' => 'admin2@open-border.com',
            'email_verified_at' => \Carbon\Carbon::now(),
            'password' => bcrypt('vUN7E@!2v5tJSyZ'),
        ]);
    }
}
