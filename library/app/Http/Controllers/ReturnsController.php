<?php

namespace App\Http\Controllers;

use App\Events\BookAvailable;
use Illuminate\Http\Request;
use App\Models\BookRequest;
use App\Models\Fine;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    public function index(Request $request)
    {
        $query = BookRequest::with(['user', 'book'])
            ->whereNotNull('returned_date')
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('book', function ($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%");
                });
            });

        $returns = $query
            ->orderBy('returned_date', 'desc')
            ->paginate(10)->withQueryString();
        ;

        return view('returns.index', compact('returns'));
    }

    public function create(BookRequest $bookRequest)
    {
        if (auth()->id() !== $bookRequest->user_id) {
            abort(403, 'Você só pode devolver seus próprios livros.');
        }

        return view('returns.create', compact('bookRequest'));
    }
    public function store(Request $request, BookRequest $bookRequest)
    {
        $request->validate([
            'return_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = $request->file('return_photo')->store('return-photos', 'public');
        $bookRequest->update([
            'returned_date' => now(),
            'return_photo_path' => $photoPath,
            'status' => 'pending_returned',
        ]);
        return redirect()->route('dashboard')
            ->with('success', 'Solicitação de devolução enviada. Aguarde a avaliação do administrador.');
    }

    public function reviewReturn(BookRequest $bookRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($bookRequest->status !== 'pending_returned') {
            return redirect()->back()->with('error', 'Esta requisição não está aguardando devolução.');
        }

        $bookRequest->load(['book', 'user']);

        return view('returns.review-return', compact('bookRequest'));
    }

    public function approveReturn(Request $request, BookRequest $bookRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }
        $request->validate([
            'book_condition' => 'required|string|in:Excellent,Good,Bad,Damaged,Lost'
        ]);
        //calcular coima se houver
        $fineData = $bookRequest->calculateFine($request->book_condition);
        DB::transaction(function () use ($bookRequest, $request, $fineData) {
            if ($fineData['fine'] > 0) {
                Fine::create([
                    'book_request_id' => $bookRequest->id,
                    'amount' => $fineData['fine'],
                    'reason' => $fineData['reason'],
                ]);
            }

            $bookRequest->update([
                'status' => 'returned',
                'admin_confirmed_return_date' => now(),
                'actual_days' => $fineData['days_used'],
                'book_condition' => $request->book_condition,
            ]);

            $book = $bookRequest->book;
            $book->available = true;

            if ($book->isDirty('available')) {
                $book->save();
                event(new BookAvailable($book));
            }
        });

        return redirect()->route('returns.index')
            ->with('success', 'Devolução aprovada com sucesso.');
    }

    public function rejectReturn(BookRequest $bookRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $bookRequest->update([
            'status' => 'approved', // volta para aprovado, pois n aceitou a devolucao
        ]);


        return redirect()->route('requests.index')
            ->with('error', 'Devolução rejeitada. O livro deve ser reapresentado.');
    }
}
