<?php

namespace Database\Seeders;

use App\Models\books;
use App\Models\genres;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class loadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /**
        * @var insert instead using create use @var insert This can significantly reduce memory usage and improve performance.
        * @var bcrypt use of bcrypt() for passwords is fine for small datasets
        * but in large number it is more efficient to use hash passwords with something like @var str_random(10)
        */
            // Insert Users
            User::insert([
                ['name' => 'User 1', 'email' => 'user1@example.com', 'password' => bcrypt('password123'), 'role' => 1],
                ['name' => 'User 2', 'email' => 'user2@example.com', 'password' => bcrypt('password123'), 'role' => 2],
                ['name' => 'User 3', 'email' => 'user3@example.com', 'password' => bcrypt('password123'), 'role' => 3],
            ]);
        
            // Insert Genres
            $genres = [
                ['name' => 'Sci-Fi'],
                ['name' => 'Romance'],
                ['name' => 'Horror'],
                ['name' => 'Fiction'],
                ['name' => 'Non-Fiction'],
            ];
            genres::insert($genres);
        
            // Insert Books
            $books = [
                ['title' => 'Book 1', 'author' => 'Author 1', 'genre_id' => 1, 'description' => 'Description of Book 1', 'published_date' => '2020-01-01', 'img_url' => 'http://example.com/img1.jpg'],
                ['title' => 'Book 2', 'author' => 'Author 2', 'genre_id' => 2, 'description' => 'Description of Book 2', 'published_date' => '2021-01-01', 'img_url' => 'http://example.com/img2.jpg'],
                // Add more books
            ];
            books::insert($books);
        }

}
