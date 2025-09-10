<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class UpdateBooksStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::all()->each(function ($book) {
            $book->update([
                'stock' => rand(1, 20) // define stock aleatório entre 1 e 20
            ]);
        });
    }
}
