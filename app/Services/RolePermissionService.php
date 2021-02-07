<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class RolePermissionService
{
    const BASE_ROLE_PERMISSIONS = [
        'Admin' => [
            'Can be an Admin', 'Create products', 'View customers', 'View orders', 'Change order status', 'Change order tracking status', 'Add notes to order', 'Change order address', 'Change order payment address', 'Edit orders'
        ],
        'Support User' => [
            'Can be a Support User', 'View customers', 'View orders', 'Change order status', 'Add notes to order', 'Change order tracking status'
        ],
        'Customer' => [
            'Can be a Customer', 'Add products to cart', 'Submit order'
        ],
    ];

    public static function prepareBaseRoles()
    {
        DB::transaction(function () {
            self::prepareBasePermissions();
            $roles = Role::with(['permissions'])->get();
            foreach (self::BASE_ROLE_PERMISSIONS as $role => $permissions) {
                if ($roles->where('name', $role)->count() == 0 || !($roles->where('name', $role)->first()->hasAllPermissions($permissions))) {
                    self::prepareRole($role, $permissions);
                }
            }
        });
    }
    public static function prepareBasePermissions()
    {
        DB::transaction(function () {
            $all_base_permissions = array_unique(Arr::flatten(array_values(self::BASE_ROLE_PERMISSIONS)));
            $insertable_permissions = [];
            $permissions = Permission::all();
            foreach ($all_base_permissions as $permission) {
                if (($permissions->where('name', $permission)->where('guard_name', 'web')->count()) == 0) {
                    $insertable_permissions[] = ['name' => $permission, 'guard_name' => 'web'];
                }
            }
            if (count($insertable_permissions) > 0) {
                Permission::insert($insertable_permissions, ['name', 'guard_name']);
                Artisan::call('permission:cache-reset');
            }
        });
    }

    public static function prepareRole(string $role_name, array $permissions = [])
    {
        if(in_array($role_name, array_keys(self::BASE_ROLE_PERMISSIONS))){
            $permissions = array_merge($permissions, self::BASE_ROLE_PERMISSIONS[$role_name]);
        }
        DB::transaction(function () use($role_name, $permissions){
            $role = Role::with('permissions')->where('name', $role_name)->first();
            if (empty($role)) {
                $role = Role::create(['name' => $role_name]);
                self::syncPermissionsWithoutDetaching($role, $permissions);
            } elseif (!($role->hasAllPermissions($permissions))) {
                self::syncPermissionsWithoutDetaching($role, $permissions);
            }
        });
    }
    public static function syncPermissionsWithoutDetaching(object $role, array $permissions)
    {
        $role->syncPermissions(array_merge($permissions, $role->permissions()->pluck('name')->toArray()));
    }
    public static function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        if (in_array($role->name, array_keys(self::BASE_ROLE_PERMISSIONS)) || $role->users()->count() > 0) {
            throw new \Exception("This Role can not be deleted");
        }
        $role->delete();
    }
    public static function updateOrCreateRole($data_array, $id = null)
    {
        // dd($data_array['permissions']);
        DB::transaction(function () use ($data_array, $id){
            $data = [
                'name' => $data_array['name'],
            ];
            $role = Role::updateOrCreate(['id' => $id], $data);
            $role->syncPermissions($data_array['permissions']);
        });
    }
}
