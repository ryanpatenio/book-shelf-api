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
             return json_message(EXIT_FORM_NULL,'err',$validator->errors());
           
         }
 
         // Create the new user
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);
 
         // Return success response        
         return json_message(EXIT_SUCCESS,'Registered Succesfulyl!');
     }
 
    
        // Handle login and generate a new token
        public function login(Request $request)
        {
            // Validate the incoming data
            $credentials = $request->only('email', 'password');

            $remember = $request->has('remember'); // Check if 'remember' is checked
            
            // Attempt to authenticate the user
            if (auth()->attempt($credentials)) {
                $user = auth()->user();

                 // Set cookies if remember me is checked
                if ($remember) {
                    cookie()->queue('email', $request->email, 120); // Store for 120 minutes
                    cookie()->queue('remember', true, 120);
                } else {
                    cookie()->queue(cookie()->forget('email'));
                    cookie()->queue(cookie()->forget('remember'));
                }
                
                // Revoke all previous tokens for this user
                $user->tokens->each(function ($token) {
                    $token->delete();
                });

                // Create a new personal access token for the user
                $token = $user->createToken('API Token')->plainTextToken;
                
                // Return the token to the client               
                return json_message(EXIT_SUCCESS,'ok',['token'=>$token]);
            }

            // Return an unauthorized response if authentication fails
            return json_message(EXIT_BE_ERROR,'Invalid Credentials');
           
        }

 
     // Get authenticated user
     public function user(Request $request)
     {
        return response()->json($request->user());
        
     }

    // return json_message(EXIT_SUCCESS,'ok',$request->user());

     // Handle logout and revoke the token
    public function logout(Request $request){
        // Revoke the token that was used to authenticate the request
        $request->user()->currentAccessToken()->delete();
        
         // Clear the 'remember' cookies if they exist
        cookie()->queue(cookie()->forget('email'));
        cookie()->queue(cookie()->forget('remember'));

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
