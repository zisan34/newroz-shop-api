<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->apiErrorResponse(404, 'These credentials do not match our records.');
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return $this->apiResponse(200, 'Login successful', [
            'user' => $user,
            'roles' => $user->roles()->with('permissions')->get(),
            'permissions' => $user->getAllPermissions(),
            'token' => $token
        ]);

    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse(200, 'Logout successful', []);
    }
}
