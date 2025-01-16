<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class books extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'genre_id',
        'decription',
        'published_date',
        'img_url'
    ];

    /**
     * @param (User::class) where u want to link the user model
     * @param ('user_books) must be the name of the table in the database
     * @param ('book_id') foreign key of the first model where this model is belongs to Books and it must the same column name in the user_books table in the database
     * However, if you use a different name (e.g., owner_id), you must specify it in the third parameter.
     * @param ('user_id') foreign key of the second model w/c is USER
     * if you don't specify the correct table name or foreign keys, the relationship will not work as expected, and Laravel will not be able to connect the models properly.
     */

    // A book can belong to many users through the user_books table
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books', 'book_id', 'user_id');
    }

    // A book belongs to a genre
    public function genre()
    {
        return $this->belongsTo(Genres::class, 'genre_id');
    }
}
