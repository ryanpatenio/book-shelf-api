<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
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

Route::get('books', [BooksController::class, 'index']);
Route::get('booksDetails/{id}',[BooksController::class,'getBooksDetails']);




// Protected Routes for authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::middleware(['role:user'])->group(function () {

        //test only
        Route::get('user', [AuthController::class, 'user']);
        Route::get('test', [AuthController::class, 'test']);
    });

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        // Admin-specific routes
        Route::post('createBooks',[BooksController::class,'store'])->name('createBooks');
    });

    // Super Admin routes
    Route::middleware(['role:super_admin'])->group(function () {
        // Super Admin-specific routes
    });

    // Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});
