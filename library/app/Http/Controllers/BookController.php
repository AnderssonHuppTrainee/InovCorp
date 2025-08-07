<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        $validated['price'] = Crypt::encryptString($validated['price']);

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
        return view('books.show', compact('book'));
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

        $validated['price'] = Crypt::encryptString($validated['price']);

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
}
