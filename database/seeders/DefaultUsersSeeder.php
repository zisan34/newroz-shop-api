<?php

namespace Database\Seeders;

use App\Services\UserService;
use Illuminate\Database\Seeder;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        UserService::updateOrCreateAdmin([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'profile_picture' => config('settings.user_default_image'),
            'password' => 'password'
        ],[
            'email' => 'admin@app.com'
        ]);
        UserService::updateOrCreateSupportUser([
            'name' => 'Support User',
            'email' => 'support_user@app.com',
            'profile_picture' => config('settings.user_default_image'),
            'password' => 'password'
        ],[
            'email' => 'support_user@app.com'
        ]);
        UserService::updateOrCreateCustomer([
            'name' => 'Customer',
            'email' => 'customer@app.com',
            'profile_picture' => config('settings.user_default_image'),
            'password' => 'password'
        ],[
            'email' => 'customer@app.com'
        ]);
    }
}
