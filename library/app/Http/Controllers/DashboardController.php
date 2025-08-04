<?php

namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Exports\BooksExport;
use App\Exports\AuthorsExport;
use App\Exports\PublishersExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'authors' => Author::count(),
            'publishers' => Publisher::count(),
            'latestBooks' => Book::with('authors', 'publisher')
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('dashboard', compact('stats'));
    }

    public function exportBooks()
    {
        return Excel::download(new BooksExport, 'livros.xlsx');
    }

    public function exportAuthors()
    {
        return Excel::download(new AuthorsExport, 'autores.xlsx');
    }

    public function exportPublishers()
    {
        return Excel::download(new PublishersExport, 'editoras.xlsx');
    }
}
