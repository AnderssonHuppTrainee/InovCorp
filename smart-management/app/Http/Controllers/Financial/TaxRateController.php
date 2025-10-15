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

        try {
            TaxRate::create($validated);

            return redirect()
                ->route('tax-rates.index')
                ->with('success', 'Taxa de IVA criada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta taxa de IVA já está registada no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar taxa de IVA. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar taxa de IVA:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar taxa de IVA. Contacte o suporte.');
        }
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

        try {
            $taxRate->update($validated);

            return redirect()
                ->route('tax-rates.index')
                ->with('success', 'Taxa de IVA atualizada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'name')) {
                    return back()->withInput()->with('error', 'Esta taxa de IVA já está registada no sistema.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar taxa de IVA. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar taxa de IVA:', [
                'tax_rate_id' => $taxRate->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar taxa de IVA. Contacte o suporte.');
        }
    }

    public function destroy(TaxRate $taxRate)
    {
        try {
            if ($taxRate->articles()->count() > 0) {
                return back()->with('error', 'Não é possível eliminar uma taxa de IVA com artigos associados.');
            }

            $taxRateName = $taxRate->name;
            $taxRate->delete();

            return redirect()
                ->route('tax-rates.index')
                ->with('success', "Taxa de IVA \"{$taxRateName}\" eliminada com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta taxa de IVA não pode ser eliminada pois está associada a outros registos.');
            }

            return back()->with('error', 'Erro ao eliminar taxa de IVA. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar taxa de IVA:', [
                'tax_rate_id' => $taxRate->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar taxa de IVA. Contacte o suporte.');
        }
    }
}
