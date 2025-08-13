<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnBookRequest;
use GuzzleHttp\Psr7\Message;
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

    public function approve(BookRequest $request)
    {

        // verifica se o admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }


        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Apenas requisições pendentes podem ser aprovadas');
        }
        $request->update([
            'status' => 'approved',
            'actual_receipt_date' => now(),
            'actual_days' => now()->diffInDays($request->request_date),
        ]);

        return redirect()->back()->with('success', 'Requisição aprovada com sucesso.');
    }

    public function returnForm(BookRequest $bookRequest)
    {
        if (auth()->id() !== $bookRequest->user_id) {
            abort(403, 'Você só pode devolver seus próprios livros.');
        }

        if ($bookRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Este livro não está em status de devolução.');
        }

        return view('requests.return', compact('bookRequest'));
    }
    public function submitReturn(Request $request, BookRequest $bookRequest)
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
        return redirect()->route('requests.index')
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

        return view('requests.review-return', compact('bookRequest'));
    }

    public function approveReturn(Request $request, BookRequest $bookRequest)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }
        $request->validate([
            'book_condition' => 'required|string|in:Excellent,Good,Bad,Dammage'
        ]);

        $bookRequest->update([
            'status' => 'returned',
            'admin_confirmed_return_date' => now(),
            'actual_days' => $bookRequest->request_date->diffInDays(now()),
            'book_condition' => $request->book_condition,
        ]);

        return redirect()->route('requests.index')
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


