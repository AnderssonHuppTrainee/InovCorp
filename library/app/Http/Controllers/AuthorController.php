<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Author::query()
            ->withCount('books')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->sort, function ($q) use ($request) {
                switch ($request->sort) {
                    case 'books_count':
                        $q->orderBy('books_count', $request->direction ?? 'asc');
                        break;

                    case 'book':
                        $q->select('authors.*')
                            ->join('author_book', 'authors.id', '=', 'author_book.author_id')
                            ->join('books', 'author_book.book_id', '=', 'books.id')
                            ->groupBy('authors.id')
                            ->orderByRaw('MIN(books.name) ' . ($request->direction ?? 'asc'));
                        break;

                    default:
                        $q->orderBy($request->sort, $request->direction ?? 'asc');
                }
            }, function ($q) {
                $q->orderBy('name', 'asc');
            });

        $authors = $query->paginate(10)->withQueryString();
        return view('authors.index', compact('authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);
        // upload da foto se existir
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        }
        //cria  e salva 
        Author::create($validated);

        return redirect()->route('/authors')->with('sucess', 'Autor criado com sucesso|');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        // carrega os livros do autor 
        $author->load('books');

        return view('authors.show', [
            'author' => $author,
            'books' => $author->books()->with('publisher')->paginate(5) // Paginação opcional
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // upload da nova foto 
        if ($request->hasFile('photo')) {
            // remove a foto antiga se existir
            if ($author->photo) {
                Storage::disk('public')->delete($author->photo);
            }
            $validated['photo'] = $request->file('photo')->store('authors', 'public');
        } else {
            // mante, a foto existente se nenhuma nova for enviada
            unset($validated['photo']);
        }
        //persist
        $author->update($validated);

        return redirect('/authors', )->with('sucess', 'Autor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        DB::transaction(function () use ($author) {
            //  remove todas as associacoes com livros
            $author->books()->detach();

            // remove a foto se existir
            if ($author->photo) {
                Storage::disk('public')->delete($author->photo);
            }

            // exclui o autor
            $author->delete();
        });

        return redirect('/authors')
            ->with('success', 'Autor removido com sucesso!');
    }
}
