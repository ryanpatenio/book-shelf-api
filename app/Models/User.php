<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string> 
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'email_verified_at','role','status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param (Book::class) where u want to link the user model
     * @param ('user_books) must be the name of the table in the database
     * @param ('user_id') foreign key of the first model where this model is belongs to USER and it must the same column name in the user_books table in the database
     * However, if you use a different name (e.g., owner_id), you must specify it in the third parameter.
     * @param ('book_id') foreign key of the second model w/c is Books
     * if you don't specify the correct table name or foreign keys, the relationship will not work as expected, and Laravel will not be able to connect the models properly.
     */

     
    // A user can have many books through the user_books table
    public function books()
    {
        return $this->belongsToMany(Books::class, 'user_books', 'user_id', 'book_id');
    }
}
