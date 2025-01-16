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
        //user seeder
        User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
            'role' => 1,
        ]);
        User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
            'role' => 2,
        ]);
        User::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'password' => bcrypt('password123'),
            'role' => 3,
        ]);

        $genre1 = genres::create(['name' => 'Sci-Fi']);
        $genre2 = genres::create(['name' => 'Romance']);
        $genre3 = genres::create(['name' => 'Horror']);
        $genre4 = genres::create(['name' => 'Fiction']);
        $genre5 = genres::create(['name' => 'Non-Fiction']);
        

        books::create([
            'title' => 'Book 1',
            'author' => 'Author 1',
            'genre_id' => $genre1->id,
            'description' => 'Description of Book 1',
            'published_date' => '2020-01-01',
            'img_url' => 'http://example.com/img1.jpg',
            
        ]);
        books::create([
            'title' => 'Book 2',
            'author' => 'Author 2',
            'genre_id' => $genre2->id,
            'description' => 'Description of Book 2',
            'published_date' => '2021-01-01',
            'img_url' => 'http://example.com/img2.jpg',
           
        ]);
        books::create([
            'title' => 'Book 3',
            'author' => 'Author 3',
            'genre_id' => $genre3->id,
            'description' => 'Description of Book 3',
            'published_date' => '2021-01-01',
            'img_url' => 'http://example.com/img3.jpg',
           
        ]);
        books::create([
            'title' => 'Book 4',
            'author' => 'Author 4',
            'genre_id' => $genre4->id,
            'description' => 'Description of Book 4',
            'published_date' => '2021-01-01',
            'img_url' => 'http://example.com/img4.jpg',
           
        ]);
        books::create([
            'title' => 'Book 5',
            'author' => 'Author 5',
            'genre_id' => $genre5->id,
            'description' => 'Description of Book 5',
            'published_date' => '2021-01-01',
            'img_url' => 'http://example.com/img2.jpg',
           
        ]);


    }
}
