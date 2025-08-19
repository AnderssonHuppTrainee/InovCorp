<?php

namespace App\Http\Controllers;


use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequestRequest;
use App\Mail\BookRequestConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\User;
use App\Models\BookRequest;
use App\Notifications\BookRequestNotification;
use Illuminate\Support\Facades\Auth;

class BookRequestController extends Controller
{
    public function index(Request $request)
    {

        $query = BookRequest::with(['user', 'book']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        $requests = $query->orderBy('request_date', 'desc')->get();

        return view('requests.index', compact('requests'));
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


        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('request-photos', 'public');
        }


        $bookRequest = BookRequest::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'request_date' => now(),
            'status' => 'pending',
            'photo_path' => $photoPath,
        ]);


        // Enviar emails de notificação
        try {

            Mail::to($bookRequest->user->email)
                ->send(new BookRequestConfirmation($bookRequest));

            User::where('role', 'admin')->each(function ($admin) use ($bookRequest) {
                $admin->notify(new BookRequestNotification($bookRequest));
            });

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar emails: ' . $e->getMessage());

        }
        $request->book->update([
            'avaliable' => false,
        ]);

        return redirect()->route('dashboard')
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
        $request->book->update([
            'avaliable' => false,
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
        $bookRequest->book->update([
            'avaliable' => true,
        ]);

        return redirect()->route('requests.index')
            ->with('error', 'Requisição rejeitada. Multas em atraso');
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
        $bookRequest->book->update([
            'avaliable' => true,
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


