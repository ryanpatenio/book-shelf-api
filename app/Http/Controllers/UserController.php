<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getAllUsers(Request $request){

        try {
            $users = User::where('role','!=',2)->get();
            return json_message(EXIT_SUCCESS,'ok',$users);

        } catch (\Throwable $th) {
            return handleException($th,'An error occure while fetching Users');
        }
   
    }
    public function getActiveUsers(Request $request){

        try {
            $users = User::where('role','!=',2)->where('status','0')->get();
            return json_message(EXIT_SUCCESS,'ok',$users);

        } catch (\Throwable $th) {
            return handleException($th,'An error occure while fetching Users');
        }
   
    }

    public function updateUserById(Request $request){

        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|exists:users,id',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $request->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation Errors',$validator->errors());
        }

        try {
            $user = User::findOrFail($request->id);

            $fieldsToUpdate = $request->only(['name', 'email', 'password']);
            if (isset($fieldsToUpdate['password'])) {
                $fieldsToUpdate['password'] = Hash::make($fieldsToUpdate['password']);
            }

            $user->fill($fieldsToUpdate);
            $user->save();

            //$updatedFields = $user->getChanges();

            return json_message(EXIT_SUCCESS, 'User updated successfully', $user);

        } catch (\Throwable $th) {
            return handleException($th,'An error occur while Updating User');
        }
    }

    public function updateRole(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|exists:users,id',
            'role'=> 'required'
        ]);
        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation errors',$validator->errors());
        }

       try {
            $user = User::findOrFail($request->id);
            $user->role = $request->role;

            $user->save();

            return json_message(EXIT_SUCCESS,'user role updated successfully!',$user);

       } catch (\Throwable $th) {
            return handleException($th,'Unable to update user role.');
       }
    }

    public function delete(Request $request){
        if(empty($request->only('id'))){
            return json_message(EXIT_FORM_NULL,'Validation Error',['Invalid ID']);
        }

        try {
            $user = User::findOrFail($request->id);

            $user->status = '1';
            $user->save();

            return json_message(EXIT_SUCCESS,'User Deleted Successfully!',$user);
        } catch (\Throwable $th) {
            return handleException($th,'An error occured while deleting User');
        }

    }

    public function restoreUser(Request $request){
        if(empty($request->only('id'))){
            return json_message(EXIT_FORM_NULL,'Validation Error',['Invalid ID']);
        }

        try {
            $user = User::findOrfail($request->id);
            $user->status = '0';
            $user->save();

            return json_message(EXIT_SUCCESS,'user restored successfully!',$user);  
        } catch (\Throwable $th) {
           return handleException($th,'Unable to restore user');
        }
    }
}
