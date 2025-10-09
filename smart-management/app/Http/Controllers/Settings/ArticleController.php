<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Core\Article;
use App\Models\Financial\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('taxRate')
            ->filter($request->only(['search', 'status']))
            ->orderBy('reference')
            ->paginate(20);

        return Inertia::render('settings/articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/articles/Create', [
            'taxRates' => TaxRate::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|unique:articles',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'tax_rate_id' => 'required|exists:tax_rates,id',
            'photo' => 'nullable|image|max:2048',
            'observations' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('articles', 'public');
        }

        $article = Article::create($validated);

        return redirect()->route('articles.index')->with('success', 'Artigo criado!');
    }

    public function show(Article $article)
    {
        return Inertia::render('settings/articles/Show', ['article' => $article->load('taxRate')]);
    }

    public function edit(Article $article)
    {
        return Inertia::render('settings/articles/Edit', [
            'article' => $article->load('taxRate'),
            'taxRates' => TaxRate::active()->get(),
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'reference' => 'required|unique:articles,reference,' . $article->id,
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'tax_rate_id' => 'required|exists:tax_rates,id',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            if ($article->photo)
                Storage::disk('public')->delete($article->photo);
            $validated['photo'] = $request->file('photo')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()->route('articles.index')->with('success', 'Artigo atualizado!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Artigo eliminado!');
    }
}
