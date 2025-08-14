<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookRequest;

class ReturnsController extends Controller
{
    public function index(Request $request)
    {
        $query = BookRequest::with(['user', 'book'])
            ->whereNotNull('returned_date') // só devoluções
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('book', function ($q) use ($search) {
                    $q->where('numero', 'like', "%{$search}%");
                })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

        $returns = $query
            ->orderBy('return_date', 'desc')
            ->paginate(10)->withQueryString();
        ;

        return view('returns.index', compact('returns'));
    }
}
