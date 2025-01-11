<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class genres extends Model
{
    use HasFactory;

     // Define the relationship to the 'books' table (one-to-many)
     public function books()
     {
         return $this->hasMany(Books::class);
     }
}
