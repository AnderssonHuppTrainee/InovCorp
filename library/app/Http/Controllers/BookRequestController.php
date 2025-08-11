<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequestRequest;
use App\Mail\BookRequested;
use App\Mail\ReturnReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Auth;

class BookRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Contagem de requisições ativas
        $activeRequestsCount = $user->isAdmin()
            ? BookRequest::whereIn('status', ['pending', 'approved'])->count()
            : $user->requests()->whereIn('status', ['pending', 'approved'])->count();

        // Contagem dos últimos 30 dias
        $recentRequestsCount = $user->isAdmin()
            ? BookRequest::where('request_date', '>=', now()->subDays(30))->count()
            : $user->requests()->where('request_date', '>=', now()->subDays(30))->count();

        // Contagem de entregues hoje
        $returnedTodayCount = $user->isAdmin()
            ? BookRequest::whereDate('actual_receipt_date', today())->count()
            : $user->requests()->whereDate('actual_receipt_date', today())->count();

        // Lista de requisições com relacionamento do livro
        $requests = $user->isAdmin()
            ? BookRequest::with('book')->latest()->get()
            : $user->requests()->with('book')->latest()->get();

        return view('requests.index', compact(
            'requests',
            'activeRequestsCount',
            'recentRequestsCount',
            'returnedTodayCount'
        ));
    }

    public function create(Book $book)
    {
        return view('requests.create', compact('book'));

    }

    public function store(StoreBookRequestRequest $request)
    {
        // Verificar se o usuário pode fazer mais requisições
        if (!auth()->user()->canRequestMoreBooks()) {
            return back()->with('error', 'Você já tem 3 livros requisitados. Devolva algum antes de requisitar outro.');
        }

        $book = Book::findOrFail($request->book_id);

        // Armazenar a foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('request-photos', 'public');
        }

        // Criar a requisição
        $bookRequest = BookRequest::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'request_date' => now(),
            'status' => 'pending',
            'photo_path' => $photoPath,
        ]);

        // Enviar emails de notificação
        //Mail::to(auth()->user()->email)->send(new BookRequested($bookRequest));
        //Mail::to(config('mail.admin_address'))->send(new BookRequested($bookRequest, true));

        return redirect()->route('requests.index')
            ->with('success', 'Requisição realizada com sucesso. Você receberá um email de confirmação.');
    }

    public function show(BookRequest $request)
    {
        // Verifica se o usuário tem permissão para ver esta requisição
        if (auth()->user()->isCitizen() && auth()->id() !== $request->user_id) {
            abort(403, 'Você só pode visualizar suas próprias requisições.');
        }
        $request->load(['book', 'user']);

        return view('requests.show', compact('request'));
    }

    public function approve(BookRequest $requestModel)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $requestModel->update([
            'status' => 'approved',
            'actual_receipt_date' => now(),
            'actual_days' => now()->diffInDays($requestModel->request_date),
        ]);

        return back()->with('success', 'Requisição aprovada com sucesso.');
    }

}


