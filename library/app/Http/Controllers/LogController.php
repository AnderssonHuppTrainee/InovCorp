<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with(['user'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('logs.index', compact('logs'));
    }


}
