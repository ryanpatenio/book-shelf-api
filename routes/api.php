<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserBooksController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('getAllBooks', [BooksController::class, 'index']);//books index all data of books
Route::get('booksDetails/{id}',[BooksController::class,'getBooksDetails']);

#Google Auth
Route::get('google/auth',[GoogleAuthController::class,'redirect'])->name('google-auth');
Route::get('auth/google/call-back',[GoogleAuthController::class,'callback']);


// Protected Routes for authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::middleware(['role:user'])->group(function () {

        #My Data
        Route::get('user', [AuthController::class, 'user']);

        #Books
        Route::get('getMyBooksCollection',[UserBooksController::class,'getUserBookCollection']);//get user books collection
        Route::post('addBundleOfBooksToMyCollection',[BooksController::class,'addBundleOfBooksToMyCollection'])->name('addBundle');
        Route::post('addBooksToMyCollection',[BooksController::class,'addBooksToMyCollection'])->name('addOneBook');

        #Profile
        Route::put('updateProfile',[ProfileController::class,'updateProfile']);//update user profile

    });

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        #Books
        Route::post('createBooks',[BooksController::class,'store'])->name('createBooks');//create
        Route::post('updateBooks',[BooksController::class,'updateBooks']);//use method post coz of form data in one request
        
        #Genres
        Route::get('getAllGenres',[GenreController::class,'getGenres']);
        Route::post('addNewGenreToBooks',[BooksController::class,'addGenreToBook']);#will add new Genre in the Books
        Route::get('getGenreById/{id}',[GenreController::class,'getGenreById'])->name('getGenreById');
        Route::put('updateGenre/{id}',[GenreController::class,'update'])->name('updateGenre');//update Genre      
        

        #Users
        Route::get('getActiveUsers',[UserController::class,'getActiveUsers']);//get Active Users
        Route::put('updateUserById',[UserController::class,'updateUserById']);//update Users
        Route::put('updateUserRole',[UserController::class,'updateRole']);//update user role

       
        #My Data
        Route::get('user', [AuthController::class, 'user']);//get my authenticated Data
        
    });

    // Super Admin routes
    Route::middleware(['role:super_admin'])->group(function () {
        #Books
        Route::post('createBooks',[BooksController::class,'store'])->name('createBooks');//create
        Route::post('updateBooks',[BooksController::class,'updateBooks']);//use method post coz of form data in one request
        

        #Genres
        Route::post('addNewGenreToBooks',[BooksController::class,'addGenreToBook']);
        Route::get('getAllGenres',[GenreController::class,'getGenres']);
        Route::get('getGenreById/{id}',[GenreController::class,'getGenreById'])->name('getGenreById');
        Route::put('updateGenre/{id}',[GenreController::class,'update'])->name('updateGenre');//update Genre

        #Users
        Route::get('getAllUsers',[UserController::class,'getAllUsers']);//get all USERS   
        Route::put('updateUserById',[UserController::class,'updateUserById']);
        Route::put('updateUserRole',[UserController::class,'updateRole']);
        Route::put('restoreUser',[UserController::class,'restoreUser']);

        #My DATA
        Route::get('user', [AuthController::class, 'user']);//get my authenticated Data

        //delete user
        Route::delete('deleteUser',[UserController::class,'delete']);
    });

    // Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});
