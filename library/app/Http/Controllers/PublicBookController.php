<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class PublicBookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()->with(['publisher', 'authors'])
            ->withAvg([
                'reviews' => function ($q) {
                    $q->where('reviews.status', 'active');
                }
            ], 'rating');

        // Filtro por termo de busca
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('isbn', 'like', '%' . $request->search . '%')
                    ->orWhereHas('authors', function ($authorQuery) use ($request) {
                        $authorQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        //  faixa de preço
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }


        $sortOptions = [
            'title_asc' => ['field' => 'name', 'direction' => 'asc'],
            'title_desc' => ['field' => 'name', 'direction' => 'desc'],
            'price_asc' => ['field' => 'price', 'direction' => 'asc'],
            'price_desc' => ['field' => 'price', 'direction' => 'desc'],
            'newest' => ['field' => 'created_at', 'direction' => 'desc'],
        ];

        $sort = $sortOptions[$request->sort] ?? $sortOptions['title_asc'];
        $query->orderBy($sort['field'], $sort['direction']);

        $books = $query->paginate(12)->withQueryString();

        return view('public.books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book->loadAvg([
            'reviews' => function ($q) {
                $q->where('reviews.status', 'active');
            }
        ], 'rating');

        $reviews = $book->reviews()
            ->where('reviews.status', 'active')
            ->with('user')
            ->latest()
            ->paginate(5);


        $relatedBooks = $book->relatedBooks(10)
            ->loadAvg([
                'reviews' => function ($q) {
                    $q->where('reviews.status', 'active');
                }
            ], 'rating');

        return view('public.books.show', compact('book', 'reviews', 'relatedBooks'));
    }
}