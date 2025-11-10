<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanySettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('settings/company/Index', [
            'company' => Company::get(),
        ]);
    }

    public function update(Request $request)
    {
        $company = Company::get();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'tax_number' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        $company->update($validated);

        return back()->with('success', 'Configurações da empresa atualizadas com sucesso!');
    }
}
