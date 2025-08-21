<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fine;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::whereHas('bookRequest', function ($q) {
            $q->where('user_id', auth()->id());
        })->latest()->get();

        return view('fines.index', compact('fines'));
    }

    public function pay(Fine $fine)
    {
        if ($fine->bookRequest->user_id !== auth()->id()) {
            abort(403);
        }

        if ($fine->status === 'paid') {
            return back()->with('error', 'Esta multa já foi paga.');
        }

        $fine->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Multa paga com sucesso!');
    }
}
