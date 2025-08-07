<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Publisher::query();

        // busca por nome
        $query->when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        })->when(
                $request->sort,
                function ($q) use ($request) {

                    $direction = $request->has('direction')
                        ? $request->direction
                        : 'desc';

                    $q->orderBy($request->sort, $direction);
                },
                function ($q) {
                    $q->orderBy('name', 'asc');
                }
            );


        $publishers = $query->paginate(10)->withQueryString();

        return view('publishers.index', compact('publishers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('publishers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);
        // upload da foto se existir
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('publishers', 'public');
        }
        //cria  e salva 
        Publisher::create($validated);

        return redirect()->route('publishers.index')->with('sucess', 'Editora cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        // carrega os livros da editora 
        $publisher->load('books');

        return view('publishers.show', [
            'publisher' => $publisher,
            'books' => $publisher->books()->with('publisher')->paginate(5)
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher)
    {
        return view('publishers.edit', compact('publisher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publisher $publisher)
    {
        //validacao
        $validated = $request->validate([
            'name' => ['required'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // upload da nova foto 
        if ($request->hasFile('logo')) {
            // remove a foto antiga se existir
            if ($publisher->logo) {
                Storage::disk('public')->delete($publisher->logo);
            }
            $validated['logo'] = $request->file('logo')->store('publishers', 'public');
        } else {
            // mante, a foto existente se nenhuma nova for enviada
            unset($validated['logo']);
        }
        //persist
        $publisher->update($validated);

        return redirect()->route('publishers.index')->with('sucess', 'Editora atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        DB::transaction(function () use ($publisher) {

            // remove a foto se existir
            if ($publisher->logo) {
                Storage::disk('public')->delete($publisher->logo);
            }

            // exclui a editora
            $publisher->delete();
        });

        return redirect()->route('publishers.index')
            ->with('success', 'Editora removida com sucesso!');
    }
}
