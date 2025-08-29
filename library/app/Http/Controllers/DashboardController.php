<?php

namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Publisher;
use App\Exports\BooksExport;
use App\Exports\AuthorsExport;
use App\Exports\PublishersExport;
use App\Models\Review;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\Fine;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $stats = [
                'books' => Book::count(),
                'authors' => Author::count(),
                'publishers' => Publisher::count(),
                'latestBooks' => Book::with('authors', 'publisher')
                    ->latest()
                    ->take(3)
                    ->get()
            ];
            $activeRequestsCount = BookRequest::whereIn('status', ['pending'])->count();
            $recentRequestsCount = BookRequest::where('created_at', '>=', now()->subDays(30))->count();
            $returnedTodayCount = BookRequest::whereDate('admin_confirmed_return_date', today())->count();

            $requests = BookRequest::with(['user', 'book'])
                ->whereIn('status', ['pending', 'approved'])
                ->orderByDesc('request_date')
                ->paginate(5);

            $returnedBooks = BookRequest::with(['user', 'book'])
                ->whereIn('status', ['pending_returned', 'returned']) // <-- use whereIn, não where com array
                ->orderByDesc('returned_date') // desc = últimas primeiro
                ->paginate(5);
            $fines = Fine::with('bookRequest.book', 'bookRequest.user')
                ->latest()
                ->paginate(10);

            return view('dashboard.dashboard', compact(
                'stats',
                'requests',
                'activeRequestsCount',
                'recentRequestsCount',
                'returnedTodayCount',
                'returnedBooks',
                'fines'

            ));
        } else {
            $stats = [
                'total_requests' => auth()->user()->requests()->count(),
                'active_requests' => auth()->user()->requests()
                    ->whereIn('status', ['pending', 'approved'])->count(),
                'overdue_requests' => auth()->user()->requests()
                    ->where('status', 'approved')
                    ->where('expected_return_date', '<', now())
                    ->count()
            ];

            $requests = auth()->user()->requests()->with('book')->orderBy('request_date', 'desc')
                ->paginate(10);

            $fines = Fine::whereHas('bookRequest', function ($q) {
                $q->where('user_id', auth()->id());
            })->latest()->get();


            return view('dashboard.citizen', compact('stats', 'requests', 'fines'));

        }
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
