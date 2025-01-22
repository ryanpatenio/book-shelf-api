<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callBack(){
    
        try {
            // Retrieve Google user details
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if a user with this email exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update the Google ID if not already set
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            } else {
                // Create a new user if not found
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->id,
                    
                ]);
            }

            // Revoke all previous tokens for this user
            $user->tokens()->delete();

            // Generate a new Sanctum token for the Google user
            $token = $user->createToken('Google API Token')->plainTextToken;

            // Return token and user data to the client
            return json_message(EXIT_SUCCESS,'Login Successful',                    
                    [
                    'token'=>$token,
                    'user'=>$user
                    ]               
            );

        } catch (\Throwable $th) {
            // Log the error for debugging
            return handleException($th,'Unable to login using google. Please try again later.');
        
        }
    }

}
