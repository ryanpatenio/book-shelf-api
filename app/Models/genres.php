<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class genres extends Model
{
    use HasFactory;

   // A genre can have many books
   public function books()
    {
        return $this->belongsToMany(Books::class, 'book_genre','genre_id','book_id');
    }

}
