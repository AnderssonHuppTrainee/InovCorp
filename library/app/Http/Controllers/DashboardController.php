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
use App\Models\Order;
use Carbon\Carbon;

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
            $pendingOrdersCount = Order::whereIn('status', ['pending'])->count();
            $paidOrdersCount = Order::whereIn('status', ['paid'])->count();

            $monthlyLabels = [];
            $monthlyPending = [];
            $monthlyPaid = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthlyLabels[] = $month->format('M/Y');

                $monthlyPending[] = Order::where('status', 'pending')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();

                $monthlyPaid[] = Order::where('status', 'paid')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
            }

            $orders = Order::with('user')->latest()->take(10)->get(); // pega as 10 ultimas

            return view('dashboard.dashboard', compact(
                'stats',
                'orders',
                'pendingOrdersCount',
                'paidOrdersCount',
                'monthlyLabels',
                'monthlyPending',
                'monthlyPaid'

            ));
        } else {

            $orders = auth()->user()->orders()
                ->with(['user', 'items.book']) // Carrega items e o book relacionado a cada item
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('dashboard.citizen', compact('orders'));

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
