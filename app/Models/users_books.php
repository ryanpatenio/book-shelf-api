<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users_books extends Model
{
    use HasFactory;
    protected $table = 'user_books';
    
    protected $fillable = ['user_id', 'book_id'];
}
