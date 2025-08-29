<?php

namespace Database\Seeders;

use App\Models\BookRequest;
use App\Models\Review;
use App\Models\User;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Book;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        /*User::factory(10)->create();
        Publisher::factory(13)->create();
        Author::factory(15)->create();

        Book::factory(50)->hasAttached
        (Author::factory(2))
            ->create();

        BookRequest::factory('50')->create();

        Review::factory('100')->create();*/
        $users = User::all();

        Book::all()->each(function ($book) use ($users) {

            $bookRequests = BookRequest::factory(5)->create([
                'book_id' => $book->id,
                'user_id' => $users->random()->id,
            ]);


            foreach ($bookRequests as $request) {
                Review::factory()->create([
                    'book_request_id' => $request->id,
                    'user_id' => $request->user_id, // mesmo user que requisitou
                ]);
            }
        });

    }
}
