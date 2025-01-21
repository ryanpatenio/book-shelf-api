<?php

namespace App\Http\Controllers;

use App\Models\book_genre;
use App\Models\books;
use App\Models\users_books;
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

    public function addBundleOfBooksToMyCollection(Request $request){
         // Validate incoming request
        $validator = Validator::make($request->all(), [
            'book_ids' => 'required|array',
            'book_ids.*' => 'integer|exists:books,id',
        ]);
        //example in front end
        // {
        //     "book_ids": [1, 2, 3, 4]
        // }

        if ($validator->fails()) {
            return json_message(EXIT_FORM_NULL, 'Validation Error', $validator->errors());
        }

        try {
            $userId = $request->user()->id;
            $bookIds = $request->book_ids;

            // Retrieve existing books in the user's collection
            $existingBooks = users_books::where('user_id', $userId)
                ->whereIn('book_id', $bookIds)//add condition book_id IN (101, 102, 104) ex.
                ->pluck('book_id')//retrieves only the column name book_id
                ->toArray();//convert into array

            // Filter out books that are already in the collection
            $newBooks = array_diff($bookIds, $existingBooks);

            if (empty($newBooks)) {
                return json_message(EXIT_BE_ERROR, 'All selected books are already in your collection.');
            }

            // Prepare data for bulk insertion
            /**
             * @var array_map 
             * @param bookId -> $bookId: Represents each book ID in the $newBooks array.
             * @param userId -> @var use use clause allows the function to access the 
             * $userId variable, which is not defined inside the callback but is needed for the insertion.
             * @param $newBooks @var array
             * results ['
             *  user_id => 1,
             * 'book_id'=> 102,
             * 'created'=>now(),
             * 'updated_at => now()
             * '...so on!]
             */
            $dataToInsert = array_map(function ($bookId) use ($userId) {
                return [
                    'user_id' => $userId,
                    'book_id' => $bookId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $newBooks);

            // Insert new records
            users_books::insert($dataToInsert);

            return json_message(EXIT_SUCCESS, 'Books added to your collection successfully.', [
                'added_books' => $newBooks,
            ]);

        } catch (\Throwable $th) {
            return handleException($th, 'An error occurred while adding books to your collection.');
        }
    }

    public function addBooksToMyCollection(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|exists:books,id',
        ]);

        if($validator->fails()){
            return json_message(EXIT_BE_ERROR, 'Invalid or missing book ID.');
        }

        try {
            $bookId = $request->id;
            $userId = $request->user()->id;
    
            // Check if the book is already in the user's collection
            $exists = users_books::where('user_id', $userId)
                ->where('book_id', $bookId)
                ->exists();
    
            if ($exists) {
                return json_message(EXIT_BE_ERROR, 'This book is already in your collection.');
            }
            // Add book to the user's collection
            users_books::create([
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);

                return json_message(EXIT_SUCCESS, 'Book added to your collection successfully.');

        } catch (\Throwable $th) {
            return handleException($th,'An error occured while adding Books to your Collections');
        }
    }

    public function addGenreToBook(Request $request){
        $validator = Validator::make($request->all(),[
            'genre_id'=>'required|exists:genres,id',
            'book_id' => 'required|exists:books,id'
        ]); 
        if($validator->fails()){
            return json_message(EXIT_FORM_NULL,'Validation Errors',$validator->errors());
        }

        $book = Books::find($request->book_id);

        if ($book->genres()->where('genre_id', $request->genre_id)->exists()) {
            // Genre already exists for the book
           return json_message(EXIT_BE_ERROR,'This genre is already assigned to the book.');
        }
 
        try {
           
            $book_genre = new book_genre();
            $book_genre->book_id = $request->book_id;
            $book_genre->genre_id = $request->genre_id;
            $book_genre->save();
                       
            return json_message(EXIT_SUCCESS,'New Genre Added To Books Successfully!',$book_genre);

        } catch (\Throwable $th) {
            //throw $th;
            return handleException($th,'Failed to Add new Genre to Books');
        }


    }
    //store new Books
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:books,title',
            'author' => 'required|string|max:255',          
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
            
            $book->description = $request->description;
            $book->published_date = $request->published_date;
            $book->img_url = $imagePath; // Save image path
            $book->status = $request->status;
            $book->save();
    
            return json_message(EXIT_SUCCESS, 'Book saved successfully', $book);

        } catch (\Exception $e) {
            // Log the error for debugging
           return handleException($e,'Failed to save the book. Please try again later.');
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
                'title', 'author', 'description', 'published_date', 'status',
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
        $book = Books::find($id);//return null if false
    
        if (!$book) {
            return json_message(EXIT_BE_ERROR, 'Book not found');
        }
 
        try {
             // Mark the book as deleted (without actually deleting it from the database)
            $book->status = 'deleted';
            $book->save();

            return json_message(EXIT_SUCCESS, 'Book Deleted Successfully!');
        } catch (\Throwable $th) {
            return handleException($th,'An error occured while deleting Books!');
        }
    
        
    }

    public function restoreBook($id){
        $book = Books::find($id);//return nulls if false

        if (!$book) {
            return json_message(EXIT_BE_ERROR, 'Book not found');
        }

       try {
            // Restore the book by setting the status back to active
            $book->status = 'active';
            $book->save();
            return json_message(EXIT_SUCCESS, 'Book restored successfully');
       } catch (\Throwable $th) {
            return handleException($th,'An error occured while restoring Books');
       }      
    }

    
}
