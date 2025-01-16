<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request){
        $user_id = $request->user()->id;//user authenticated id

        if(empty($user_id)){
            return json_message(EXIT_FORM_NULL,'Validation Error',['invalid ID']);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user_id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation Errors',$validator->errors());
        }
        

        try {
            $user = User::findOrFail($user_id);

            $fieldsToUpdate = $request->only(['name', 'email', 'password']);
            if (isset($fieldsToUpdate['password'])) {
                $fieldsToUpdate['password'] = Hash::make($fieldsToUpdate['password']);
            }

            $user->fill($fieldsToUpdate);
            $user->save();

            return json_message(EXIT_SUCCESS, 'Profile updated successfully', $user);

        } catch (\Throwable $th) {
            return handleException($th,'An error occured while updating Profile');
        }
    }
}
