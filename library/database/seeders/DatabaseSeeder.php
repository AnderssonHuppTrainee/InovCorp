<?php

namespace Database\Seeders;

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

        User::factory(10)->create();
        Publisher::factory(13)->create();
        Author::factory(15)->create();

        Book::factory(50)->hasAttached(Author::factory(3))->create();

    }
}
