<?php

namespace App\Http\Controllers;

use App\Models\books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    public function index(){
        try {
            // Retrieve all books from the database
            $books = Books::where('status', 'active')->get();
    
    
            // Return books with success message
            return json_message(EXIT_SUCCESS, 'ok', $books);
        } catch (\Exception $e) {
            // Log the exception (optional)
            return handleException($e, 'Error fetching books');
        }
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:books,title',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|integer|exists:genres,id',
            'description' => 'required|string|max:255',
            'published_date' => 'required|date',
            'status' => 'required|in:active,inactive,pending',  // Ensure the status is one of the valid options
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
            $book->status = $request->status;
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

    public function updateBooks(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:books,id',
            'title' => 'sometimes|required|string|max:255|unique:books,title,' . $request->id,
            'author' => 'sometimes|required|string|max:255',
            'genre_id' => 'sometimes|required|integer|exists:genres,id',
            'description' => 'sometimes|required|string|max:255',
            'published_date' => 'sometimes|required|date',
            'status' => 'nullable|in:active,inactive,deleted',
            'img_url' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:50048',
        ]);
    
        if ($validator->fails()) {
            return json_message(EXIT_FORM_NULL, 'Validation Error', $validator->errors());
        }
    
        try {
            // Find the book
            $book = Books::findOrFail($request->id);
    
            // Handle image upload if present
            if ($request->hasFile('img_url')) {
                // Delete old image if it exists
                if ($book->img_url && \Storage::disk('public')->exists($book->img_url)) {
                    \Storage::disk('public')->delete($book->img_url);
                }
    
                // Store new image and get its path
                $image = $request->file('img_url');
                $uniqueName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $book->img_url = $image->storeAs('images', $uniqueName, 'public');
            }
    
            // Fill and save other fields
            $book->fill($request->only([
                'title', 'author', 'genre_id', 'description', 'published_date', 'status',
            ]));
            $book->save();
    
            return json_message(EXIT_SUCCESS, 'Book updated successfully', $book);
        } catch (\Throwable $e) {
            // Log the error
           return handleException($e,'An error occured while updating Books!');
        }
    }
    

    public function deleteBook($id)
    {
        $book = Books::find($id);
    
        if (!$book) {
            return json_message(EXIT_BE_ERROR, 'Book not found');
        }
    
        // Mark the book as deleted (without actually deleting it from the database)
        $book->status = 'deleted';
        $book->save();
    
        return json_message(EXIT_SUCCESS, 'Book Deleted Successfully!');
    }

    public function restoreBook($id){
        $book = Books::find($id);

        if (!$book) {
            return json_message(EXIT_BE_ERROR, 'Book not found');
        }

        // Restore the book by setting the status back to active
        $book->status = 'active';
        $book->save();

        return json_message(EXIT_SUCCESS, 'Book restored successfully');
    }

    
}
