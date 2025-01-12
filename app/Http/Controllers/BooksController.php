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

    public function updateBooks(Request $request){

       // Validate incoming request
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:books,id',
            'title' => 'required|string|max:255|unique:books,title,' . $request->id, // Exclude current book from unique check @it will ignore the current book being updated (based on its id), allowing the title to remain unchanged during the update.
            'author' => 'required|string|max:255',
            'genre_id' => 'required|integer|exists:genres,id',
            'description' => 'required|string|max:255',
            'published_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,pending',  // Allow updating status
            'img_url' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:50048', // max:kilobytes | Optional field for image update | sometimes => optional field present or not
        ]);

        if ($validator->fails()) {
            return json_message(EXIT_FORM_NULL, 'Validation Error', $validator->errors());
        }

            try {
                $book = Books::findOrFail($request->id);

                // Check if a new image was uploaded
                if ($request->hasFile('img_url')) {
                    // Ensure the old image is deleted if it exists
                    if ($book->img_url && \Storage::disk('public')->exists($book->img_url)) {
                        \Storage::disk('public')->delete($book->img_url);
                    }

                    // Store new image and update the path
                    $image = $request->file('img_url');
                    $uniqueName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('images', $uniqueName, 'public');

                    $book->img_url = $imagePath;
                }

                 // Update the status if it's provided
                if ($request->has('status')) {
                    $book->status = $request->status;
                }

                // Update other fields
                $book->title = $request->title;
                $book->author = $request->author;
                $book->genre_id = $request->genre_id;
                $book->description = $request->description;
                $book->published_date = $request->published_date;

                // Save updated book
                $book->save();

                return json_message(EXIT_SUCCESS, 'Book updated successfully', $book);
            } catch (\Throwable $e) {
                // Log error
                Log::error('Error updating book: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                return json_message(EXIT_BE_ERROR, 'Failed to update book');
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
