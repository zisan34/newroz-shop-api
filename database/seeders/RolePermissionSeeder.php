<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\RolePermissionService;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RolePermissionService::prepareBaseRoles();
    }
}
