<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
     // User registration
     public function register(Request $request)
     {
         // Validate the incoming data
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'password' => 'required|string|min:8',
         ]);
 
         if ($validator->fails()) {
             return response()->json($validator->errors(), 400);
         }
 
         // Create the new user
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);
 
         // Return success response
         return response()->json(['message' => 'User registered successfully'], 201);
     }
 
     // User login and return the API token
     public function login(Request $request){
        // Validate credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (auth()->attempt($request->only('email', 'password'))) {
            $user = auth()->user();
            
            // Generate token
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ], 200);
        }

    return response()->json(['message' => 'Invalid credentials'], 401);
}
 
     // Get authenticated user
     public function user(Request $request)
     {
         return response()->json($request->user());
     }
}
