<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $allBooks = Book::latest()->take(8)->get(); // 
        $latestBooks = Book::with([
            'authors',
            'publisher',
            'reviews' => function ($q) {
                $q->where('reviews.status', 'active')
                    ->latest('reviews.created_at')
                    ->take(5); // so as 5 mais recentes
            },
            'reviews.user'
        ])->latest()->take(10)->get();


        return view('welcome', compact('latestBooks', 'allBooks'));

    }
}
