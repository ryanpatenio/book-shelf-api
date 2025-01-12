<?php

namespace App\Http\Controllers;

use App\Models\books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    public function index(){
        try {
            // Retrieve all books from the database
            $books = Books::all();
    
            // Check if any books were found
            if ($books->isEmpty()) {
                return json_message(EXIT_SUCCESS, 'No books found', []);
            }
    
            // Return books with success message
            return json_message(EXIT_SUCCESS, 'ok', $books);
        } catch (\Exception $e) {
            // Log the exception (optional)
          
            Log::error('Error fetching books: ' . $e->getMessage());
    
            // Return an error message
            return json_message(EXIT_BE_ERROR, 'Failed to fetch books', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:books,title',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|integer|exists:genres,id',
            'description' => 'required|string|max:255',
            'published_date' => 'required|date',
            'img_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return json_message(EXIT_FORM_NULL, 'Validation errors', $validator->errors());
        }
    
        try {
            // Handle image upload with a unique name
            $imagePath = null;
            if ($request->hasFile('img_url')) {
                $image = $request->file('img_url');
                $uniqueName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images', $uniqueName, 'public'); // Save to storage/app/public/images
            }
    
            // Save data to the database
            $book = new books();
            $book->title = $request->title;
            $book->author = $request->author;
            $book->genre_id = $request->genre_id;
            $book->description = $request->description;
            $book->published_date = $request->published_date;
            $book->img_url = $imagePath; // Save image path
            $book->save();
    
            return json_message(EXIT_SUCCESS, 'Book saved successfully', $book);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error saving book: ' . $e->getMessage());
    
            // Return a generic error response to the client
            return json_message(EXIT_BE_ERROR, 'Failed to save the book. Please try again later.');
        }

    }

    public function getBooksDetails($id){
        
        $validated = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:books,id',
        ]);
    
        if ($validated->fails()) {
            return json_message(EXIT_BE_ERROR, 'Invalid or missing book ID.');
        }
    
        $book = Books::find($id);
        return json_message(EXIT_SUCCESS, 'Book details retrieved successfully.', $book);
    }

    
}
