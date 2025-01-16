<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\GenreController;
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

Route::get('books', [BooksController::class, 'index']);//books index all data of books
Route::get('booksDetails/{id}',[BooksController::class,'getBooksDetails']);

Route::get('getGenres',[GenreController::class,'getGenres']);
Route::get('getGenreById/{id}',[GenreController::class,'getGenreById'])->name('getGenreById');



// Protected Routes for authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::middleware(['role:user'])->group(function () {

        //get user authenticated
        Route::get('user', [AuthController::class, 'user']);

        //add Books in user collections
        Route::post('addBundleOfBooksToMyCollection',[BooksController::class,'addBundleOfBooksToMyCollection'])->name('addBundle');
        Route::post('addBooksToMyCollection',[BooksController::class,'addBooksToMyCollection'])->name('addOneBook');


        Route::get('getUserBookCollection',[UserBooksController::class,'getUserBookCollection']);//get user books collection
        Route::put('updateProfile',[ProfileController::class,'updateProfile']);//update user profile

    });

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        // Admin-specific routes //patch partial update
        Route::post('createBooks',[BooksController::class,'store'])->name('createBooks');//create
        Route::post('updateBooks',[BooksController::class,'updateBooks']);//use method post coz of form data in one request
        Route::put('updateGenre/{id}',[GenreController::class,'update'])->name('updateGenre');//update Genre

        Route::get('getActiveUsers',[UserController::class,'getActiveUsers']);//get Active Users
        Route::put('updateUserById',[UserController::class,'updateUserById']);//update Users
        
    });

    // Super Admin routes
    Route::middleware(['role:super_admin'])->group(function () {
        // Super Admin-specific routes
        Route::get('getAllUsers',[UserController::class,'getAllUsers']);//get all USERS   
        Route::get('user', [AuthController::class, 'user']);//get my authenticated Data

        //update users
        Route::put('updateUserById',[UserController::class,'updateUserById']);

        //delete user
        Route::delete('delete',[UserController::class,'delete']);
    });

    // Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});
