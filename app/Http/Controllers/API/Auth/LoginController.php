<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use ApiResponse;

    public function login(Request $request)
    {
         $request->validate([
             'email' => 'required|string|email',
             'password' => 'required|string',
         ]);

         if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
             return $this->error('The provided credentials do not match our records.',401);
         }

         if (Auth::user()->email_verified_at === null){
             return $this->error('Email not verified.',403);
         }

         $user = Auth::user();

         return response()->json([
             'status' => true,
             'message' => 'Login Successful',
             'token_type' => 'Bearer',
             'token' => $user->createToken('AuthToken')->plainTextToken,
             'data' => $user
         ]);
    }

    public function logout(Request $request)
    {
        try {
            // the user token
            $request->user()->currentAccessToken()->delete();
            // logged out
            return $this->ok('Logged out successfully.');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(),500);
        }
    }

    public function userDetails()
    {
        $user = Auth::user();

        return $this->ok('User Details fetch successfully.', $user);
    }

}
