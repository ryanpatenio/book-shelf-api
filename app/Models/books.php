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

    // Define the relationship to the 'genres' table (one-to-many)
    public function genre()
    {
        return $this->belongsTo(Genres::class);
    }

    // Define the relationship to the 'users' through the 'user_books' pivot table (many-to-many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')// model users
                    ->withTimestamps();
    }
}
