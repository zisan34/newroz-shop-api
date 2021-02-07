<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    //
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users',
            'name' => 'required|string',
            'password' => 'required|confirmed|string',
            'phone' => 'nullable|string',
            'profile_picture' => 'nullable|file|image|max:1000',
        ]);
        // dd($request->all());
        
        try {
            DB::beginTransaction();
            $user = UserService::updateOrCreateCustomer($request->all());
            $token = $user->createToken('my-app-token')->plainTextToken;
            
            DB::commit();
            return $this->apiResponse(200, 'Login successful', [
                'user' => $user,
                'roles' => $user->roles()->with('permissions')->get(),
                'permissions' => $user->getAllPermissions(),
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->apiErrorResponse(422, $th->getMessage());
        }
    }
}
