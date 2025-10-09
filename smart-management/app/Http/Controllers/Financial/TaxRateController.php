<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\TaxRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        $taxRates = TaxRate::query()
            ->withCount('articles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(isset($request->is_active) && $request->is_active !== 'all', function ($query) use ($request) {
                $query->where('is_active', $request->is_active === '1');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('settings/tax-rates/Index', [
            'taxRates' => $taxRates,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/tax-rates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tax_rates,name',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        TaxRate::create($validated);

        return redirect()
            ->route('tax-rates.index')
            ->with('success', 'Taxa de IVA criada com sucesso!');
    }

    public function show(TaxRate $taxRate)
    {
        $taxRate->loadCount('articles');

        return Inertia::render('settings/tax-rates/Show', [
            'taxRate' => $taxRate,
        ]);
    }

    public function edit(TaxRate $taxRate)
    {
        return Inertia::render('settings/tax-rates/Edit', [
            'taxRate' => $taxRate,
        ]);
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tax_rates,name,' . $taxRate->id,
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $taxRate->update($validated);

        return redirect()
            ->route('tax-rates.index')
            ->with('success', 'Taxa de IVA atualizada com sucesso!');
    }

    public function destroy(TaxRate $taxRate)
    {
        // Check if tax rate has articles
        if ($taxRate->articles()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar uma taxa de IVA com artigos associados.');
        }

        $taxRate->delete();

        return redirect()
            ->route('tax-rates.index')
            ->with('success', 'Taxa de IVA eliminada com sucesso!');
    }
}
