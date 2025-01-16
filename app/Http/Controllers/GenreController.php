<?php

namespace App\Http\Controllers;

use App\Models\genres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    

    public function getGenres(){

        try {
            $data = genres::all();

            return json_message(EXIT_SUCCESS,'ok',$data);

        } catch (\Throwable $th) {
            //throw $th;
            return handleException($th, 'Error fetching Genre');
        }       
       
    }

    public function getGenreById($id){
        $validator = Validator::make(['id'=>$id],[
            'id' => 'required|integer|exists:genres,id',
        ]);

        if($validator->fails()){
            return json_message(EXIT_BE_ERROR,'Validation Error',$validator->errors());
        }
        try {
            $genre = Genres::findOrFail($id);

            return json_message(EXIT_SUCCESS,'ok',$genre);
        } catch (\Throwable $th) {
            return handleException($th,'An error occured while fetching Genre');
        }

    }
    public function update(Request $request,$id){

        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|exists:genres,id',
            'name' => 'sometimes|required|string|max:255|unique:genres,name,'.$id,
            
        ]);

        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation errors',$validator->errors());
        }

        try {
            //save
            $genres = Genres::findOrFail($id);

            $genres->name = $request->name;

            $genres->save();

            return json_message(EXIT_SUCCESS,'Genre Updated Sucessfully!',$genres);

        } catch (\Throwable $th) {
            return handleException($th,'An error occurred while updating the genre');
        }
    }

    public function detele($id){
        $validator = Validator::make(['id'=>$id],[
            'id' => 'required|integer|exists:genres,id'
        ]);
        if($validator->fails()){
            return json_message(EXIT_BE_ERROR,'Validator Error',$validator->errors());
        }

        try {
            $genre = Genres::findOrFail($id);
            $genre->status = 'deleted';
            
            $genre->save();

            return json_message(EXIT_SUCCESS,'Deleted Successfully!');
        } catch (\Throwable $th) {
            return handleException($th);
        }
    }
}
