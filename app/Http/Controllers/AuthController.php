<?php

namespace App\Http\Controllers;

use App\Models\books;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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
 
    
        // Handle login and generate a new token
        public function login(Request $request)
        {
            // Validate the incoming data
            $credentials = $request->only('email', 'password');
            
            // Attempt to authenticate the user
            if (auth()->attempt($credentials)) {
                $user = auth()->user();
                
                // Revoke all previous tokens for this user
                $user->tokens->each(function ($token) {
                    $token->delete();
                });

                // Create a new personal access token for the user
                $token = $user->createToken('API Token')->plainTextToken;
                
                // Return the token to the client
                return response()->json(['token' => $token]);
            }

            // Return an unauthorized response if authentication fails
            return response()->json(['message' => 'Unauthorized'], 401);
        }

 
     // Get authenticated user
     public function user(Request $request)
     {
         return response()->json($request->user());
     }

     // Handle logout and revoke the token
    public function logout(Request $request){
        // Revoke the token that was used to authenticate the request
        $request->user()->currentAccessToken()->delete();

        // Optionally, return a success message
        return response()->json(['message' => 'Logged out successfully']);
    }


     public function test(){
        $user = books::where('id',4)->get();

        return response()->json([
            'code'=>1,
            'data'=>$user
        ]);
     }
}
