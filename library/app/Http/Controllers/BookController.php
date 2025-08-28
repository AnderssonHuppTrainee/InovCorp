<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookNotification;
use Exception;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Exists;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'publisher']) // eager loading
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('isbn', 'like', '%' . $request->search . '%');
            })
            ->when($request->author, function ($q) use ($request) {
                $q->whereHas('authors', fn($sub) => $sub->where('id', $request->author));
            })
            ->when($request->publisher, function ($q) use ($request) {
                $q->where('publisher_id', $request->publisher);
            })
            ->when($request->sort, function ($q) use ($request) {
                switch ($request->sort) {
                    case 'publisher':
                        $q->join('publishers', 'books.publisher_id', '=', 'publishers.id')
                            ->orderBy('publishers.name', $request->direction ?? 'asc');
                        break;
                    case 'author':
                        $q->join('author_book', 'books.id', '=', 'author_book.book_id')
                            ->join('authors', 'author_book.author_id', '=', 'authors.id')
                            ->groupBy('books.id')
                            ->orderByRaw('MIN(authors.name) ' . ($request->direction ?? 'asc'));
                        break;

                    case 'price':
                        $q->orderBy('price', $request->direction ?? 'asc');
                        break;

                    default:
                        $q->orderBy($request->sort, $request->direction ?? 'asc');
                }
            }, fn($q) => $q->orderBy('name'));

        $books = $query->paginate(10)->withQueryString();

        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('books.index', compact('books', 'authors', 'publishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $publishers = Publisher::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();

        return view('books.create', compact('publishers', 'authors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'unique:books,isbn'],
            'publisher_id' => ['required', 'exists:publishers,id'],
            'authors' => ['required', 'array'],
            'authors.*' => ['exists:authors,id'],
            'bibliography' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0']
        ]);

        // upload da foto se existir
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        //cria  e salva 
        $book = Book::create($validated);
        $book->authors()->sync($request->authors);

        return redirect()->route('book.index')->with('sucess', 'Livro cadastrado com sucesso|');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $requests = $book->requests()
            ->with('user')
            ->latest()
            ->paginate(10);
        return view('books.show', compact('book', 'requests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $book->load('authors');//carregar os autores


        return view('books.edit', [
            'book' => $book,
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'price' => $book->price,
            'selectedAuthors' => $book->authors->pluck('id')->toArray()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'unique:books,isbn' . $book->id],
            'publisher_id' => ['required', 'exists:publishers,id'],
            'authors' => ['required', 'array'],
            'authors.*' => ['exists:authors,id'],
            'bibliography' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0']
        ]);


        // atualiza  se foi enviada
        if ($request->hasFile('cover_image')) {
            // remove antiga se existir
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        //atualiza 
        $book->update($validated);
        //sync com authors
        $book->authors()->sync($request->authors);

        return redirect()->route('books.show', $book->id)
            ->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        DB::transaction(function () use ($book) {
            //  remove todas as associacoes com autores
            $book->authors()->detach();

            // remove a foto se existir
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // exclui o livro
            $book->delete();
        });

        return redirect()->route('books.index')
            ->with('success', 'Livro removido com sucesso!');
    }

    public function import(Request $request)
    {
        $books = [];

        if ($request->filled('title') || $request->filled('author') || $request->filled('isbn')) {
            $query = '';
            if ($request->filled('title'))
                $query .= 'intitle:' . $request->title . ' ';
            if ($request->filled('author'))
                $query .= 'inauthor:' . $request->author . ' ';
            if ($request->filled('isbn'))
                $query .= 'isbn:' . $request->isbn . ' ';

            $startIndex = $request->get('startIndex', 0);

            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => trim($query),
                'key' => config('services.google_books.key'),
                'maxResults' => 12,
                'startIndex' => $startIndex,
            ]);

            $books = $response->json()['items'] ?? [];

        }

        return view('books.import', compact('books'));
    }

    public function searchGoogle(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('books.import')->with('error', 'Digite um termo para pesquisar.');
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'maxResults' => 10, // máximo 40
        ]);

        $books = $response->json()['items'] ?? [];

        return view('books.results', compact('books'));
    }
    public function storeGoogle(Request $request)
    {
        try {
            $bookData = $request->input('book');

            if (!$bookData) {
                return redirect()->back()->with('error', 'Nenhum livro selecionado para importar!');
            }

            $info = json_decode($bookData, true);

            //verifica se o livro ja existe
            $isbn = $info['industryIdentifiers'][0]['identifier'] ?? null;

            if ($isbn) {
                $existingBook = Book::where('isbn', $isbn)->first();
                if ($existingBook) {
                    return redirect()->back()->with('error', 'Livro já existente na base de dados!');
                }
            }
            //cria editora
            $publisherName = $info['publisher'] ?? 'Desconhecido';
            $publisher = Publisher::firstOrCreate(
                ['name' => $publisherName]
            );
            //registra livro
            $book = Book::create([
                'isbn' => $info['industryIdentifiers'][0]['identifier'] ?? 'NOISBN-' . uniqid(),
                'name' => $info['title'] ?? 'Sem título',
                'publisher_id' => $publisher->id,
                'bibliography' => $info['description'] ?? 'Descrição não disponível',
                'cover_image' => $info['imageLinks']['thumbnail'] ?? null,
                'price' => rand(1, 50),
                'available' => true,
            ]);


            $authors = $info['authors'] ?? ['Desconhecido'];

            foreach ($authors as $authorName) {
                $author = Author::firstOrCreate([
                    'name' => $authorName
                ]);

                // vincula autor ao livro via pivot
                $book->authors()->syncWithoutDetaching($author->id);
            }


            return redirect()->back()
                ->with('success', 'Livro importado com sucesso!');

        } catch (Exception $e) {
            \Log::error('Erro ao importar livros: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Falha ao importar os livros! Detalhes: ' . $e->getMessage());
        }
    }

    public function notify(Book $book)
    {

        //cria a notificaçao 
        $notification = BookNotification::firstOrCreate([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
        ]);
        if ($notification->wasRecentlyCreated) {
            return back()->with('success', 'Você será notificado quando este livro estiver disponível!');
        }
        return back()->with('info', 'Você já tinha solicitado notificação para este livro.');
    }
}