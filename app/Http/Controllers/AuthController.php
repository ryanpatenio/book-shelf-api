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
             return json_message(EXIT_FORM_NULL,'Validation Error',$validator->errors());
           
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
 
    
     public function login(Request $request)
     {
         // Validate the incoming request data
       
         $validator = Validator::make($request->all(),[
             'email' => 'required|email',
             'password' => 'required',
             'remember' => 'sometimes|boolean',
         ]);

        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation error',$validator->errors());
        }
     
         $credentials = $request->only('email', 'password');
         $remember = $request->boolean('remember', false); // Default to false if 'remember' is not present
     
         // Attempt to authenticate the user
         if (auth()->attempt($credentials)) {
             $user = auth()->user();
     
             // Manage "Remember Me" cookies
             if ($remember) {
                 cookie()->queue('email', $request->email, 120); // Store for 120 minutes
                 cookie()->queue('remember', true, 120);
             } else {
                 cookie()->queue(cookie()->forget('email'));
                 cookie()->queue(cookie()->forget('remember'));
             }
     
             // Revoke all previous tokens for this user
             $user->tokens()->delete();
     
             // Create a new personal access token for the user
             $token = $user->createToken('API Token')->plainTextToken;
     
             // Return the token and user data to the client
             return json_message(EXIT_SUCCESS,'Login Successful',                    
                    [
                    'token'=>$token,
                    'user'=>$user
                    ]               
            );
            
         }
     
         // Return an unauthorized response if authentication fails
         return json_message(EXIT_BE_ERROR,'Invalid creadentials');
       
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
