<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Mail\BookRequestConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Models\Book;
use App\Models\User;
use App\Models\BookRequest;
use App\Notifications\BookRequestNotification;
use App\Events\BookAvailable;

class BookRequestController extends Controller
{
    public function index(Request $request)
    {

        $query = BookRequest::with(['user', 'book']);

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
            });
        }
        $bookRequests = $query->orderBy('request_date', 'desc')
            ->paginate(10)->withQueryString();
        ;

        return view('requests.index', compact('bookRequests'));
    }

    public function create(Book $book)
    {
        return view('requests.create', compact('book'));

    }

    public function store(StoreBookRequest $bookRequest)
    {
        // verifica se pode fazer mais requisições
        if (!auth()->user()->canRequestMoreBooks()) {
            return back()->with('error', 'Você já tem 3 livros requisitados. Devolva algum antes de requisitar outro.');
        }

        $book = Book::findOrFail($bookRequest->book_id);

        if (!$book->isAvailable()) {
            return back()->with('error', 'Este livro já foi requisitado e não está disponível.');
        }

        $photoPath = null;
        if ($bookRequest->hasFile('photo')) {
            $photoPath = $bookRequest->file('photo')->store('request-photos', 'public');
        }


        $bookRequest = BookRequest::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'request_date' => now(),
            'expected_return_date' => now()->addDays(5),
            'status' => 'pending',
            'photo_path' => $photoPath,
        ]);


        // enviar emails de notificação
        try {

            Mail::to($bookRequest->user->email)
                ->send(new BookRequestConfirmation($bookRequest));

            User::where('role', 'admin')->each(function ($admin) use ($bookRequest) {
                $admin->notify(new BookRequestNotification($bookRequest));
            });

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar emails: ' . $e->getMessage());

        }
        $book->update([
            'available' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Requisição realizada com sucesso.
            Você receberá um email de confirmação.');
    }

    public function show(BookRequest $bookRequest)
    {
        //verificação
        if (auth()->user()->isCitizen() && auth()->id() !== $bookRequest->user_id) {
            abort(403, 'Você só pode visualizar suas próprias requisições.');
        }
        $bookRequest->load(['book', 'user']);

        return view('requests.show', compact('bookRequest'));
    }

    public function approve(BookRequest $bookRequest)
    {

        // verifica se e o admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        if ($bookRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Apenas requisições pendentes podem ser aprovadas');
        }
        $bookRequest->update([
            'status' => 'approved',
            'actual_receipt_date' => now(),
            'expected_return_date' => now()->addDays(5),
        ]);
        $bookRequest->book->update([
            'available' => false,
        ]);

        return redirect()->back()->with('success', 'Requisição aprovada com sucesso.');
    }

    public function reject(BookRequest $bookRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        $bookRequest->update([
            'status' => 'rejected', // rejeitado
        ]);
        $book = $bookRequest->book;
        $book->available = true;

        if ($book->isDirty('available')) {
            $book->save();
            event(new BookAvailable($book));
        }
        ;

        return redirect()->route('requests.index')
            ->with('error', 'Requisição rejeitada. Multas em atraso');
    }


    public function cancel(BookRequest $bookRequest)
    {

        if (auth()->id() !== $bookRequest->user_id) {
            abort(403, 'Ação não autorizada.');
        }

        $bookRequest->update([
            'status' => 'cancelled',
        ]);


        return redirect()->back()->with('success', 'Requisição cancelada com sucesso.');
    }

}


