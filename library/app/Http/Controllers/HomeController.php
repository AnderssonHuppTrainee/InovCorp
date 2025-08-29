<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $allBooks = Book::withAvg([
            'reviews as reviews_avg_rating' => function ($q) {
                $q->where('reviews.status', 'active');
            }
        ], 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(8)
            ->get();


        $latestBooks = Book::with([
            'authors',
            'publisher',
            'reviews' => function ($q) {
                $q->where('reviews.status', 'active')
                    ->latest('reviews.created_at')
                    ->take(5);
            },
            'reviews.user'
        ])
            ->withAvg([
                'reviews as reviews_avg_rating' => function ($q) {
                    $q->where('reviews.status', 'active');
                }
            ], 'rating')
            ->latest()
            ->take(10)
            ->get();


        return view('welcome', compact('latestBooks', 'allBooks'));

    }
}
