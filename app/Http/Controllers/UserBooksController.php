<?php

namespace App\Http\Controllers;

use App\Models\books;
use App\Models\User;
use Illuminate\Http\Request;

class UserBooksController extends Controller
{
    public function getUserBookCollection(Request $request){
        $user_id = $request->user()->id;
        if(empty($user_id)){
            return json_message(EXIT_FORM_NULL,'Validation Errors',['id is invalid']);
        }
        
        try {
            $userBooks = User::find($user_id)->books()->with('genres')->get();
          
           
            return json_message(EXIT_SUCCESS,'ok',$userBooks);
        } catch (\Throwable $th) {
            return handleException($th,'An error occured while fetching your Books Collection');
        }
       
    }
  
}
