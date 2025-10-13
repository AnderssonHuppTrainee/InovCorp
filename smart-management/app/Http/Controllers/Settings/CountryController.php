<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Country;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status === 'active');
        }

        $countries = $query->orderBy('name')->paginate(10);

        return Inertia::render('settings/countries/Index', [
            'countriesData' => $countries,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('settings/countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        Country::create($request->validated());

        return redirect()->route('countries.index')
            ->with('success', 'País criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        $country->load('entities');

        return Inertia::render('settings/countries/Show', [
            'country' => $country,
            'entitiesCount' => $country->entities()->count(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return Inertia::render('settings/countries/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        $country->update($request->validated());

        return redirect()->route('countries.index')
            ->with('success', 'País atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        if ($country->entities()->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar este país, pois existem entidades associadas.');
        }

        $country->delete();

        return redirect()->route('countries.index')
            ->with('success', 'País eliminado com sucesso.');
    }
}

