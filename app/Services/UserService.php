<?php
namespace App\Services;

use App\Models\User;

use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Hash;
use App\Services\RolePermissionService;

class UserService{
    public static function decorateAttrs($attrs = null){
        if(gettype($attrs) == 'array'){
            return $attrs;
        }
        elseif(gettype($attrs) == "integer"){
            $attrs = ['id' => $attrs];
        }elseif(gettype($attrs) == "NULL" || false == $attrs ){
            $attrs = ['id' => null];
        }else{
            throw new \Exception('the variable $attrs can have integer as id or array of attributes or null only');
        }
        return $attrs;
    }
	public static function updateOrCreateAdmin(array $data_array, $attrs = null){
        try {
            DB::beginTransaction();

            RolePermissionService::prepareRole('Admin', ['Can be an Admin']);
            $user = self::updateOrCreateUser($data_array, $attrs);
            $user->syncRoleWithoutDetaching(['Admin']);

            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            DB::rollback(); 
            throw $th;
        }
	}
	public static function updateOrCreateSupportUser(array $data_array, $attrs = null){
        try {
            DB::beginTransaction();
            RolePermissionService::prepareRole('Support User', ['Can be a Support User']);
            $user = self::updateOrCreateUser($data_array, $attrs);
            $user->syncRoleWithoutDetaching(['Support User']);

            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            DB::rollback(); 
            throw $th;
        }
	}
	public static function updateOrCreateCustomer(array $data_array, $attrs = null){
        try {
            DB::beginTransaction();

            RolePermissionService::prepareRole('Customer');
            $user = self::updateOrCreateUser($data_array, $attrs);
            $user->syncRoleWithoutDetaching(['Customer']);

            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            DB::rollback(); 
            throw $th;
        }
	}

	public static function updateOrCreateUser(array $data_array, $attrs = null)
	{
        try {
            DB::beginTransaction();

            $insertable_data = collect($data_array)->only(['name', 'email', 'phone', 'profile_picture'])->toArray();
            $insertable_data['password'] = Hash::make($data_array['password']);
            if(request()->hasFile('profile_picture')){
                $insertable_data['profile_picture'] = FileUploadService::upload(request()->profile_picture, 'uploads/profile_pictures');
            }
            $user = User::updateOrCreate(self::decorateAttrs($attrs), $insertable_data);

            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            DB::rollback();
            if(array_key_exists('profile_picture', $data_array) && request()->hasFile('profile_picture')){
                FileUploadService::delete($data_array['profile_picture']);
            }
            throw $th;
        }
	}
}
