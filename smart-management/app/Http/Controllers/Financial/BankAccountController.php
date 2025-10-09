<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Models\Financial\BankAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accounts = BankAccount::query()
            ->filter($request->only(['search', 'is_active']))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('financial/bank-accounts/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('financial/bank-accounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankAccountRequest $request)
    {
        try {
            $account = BankAccount::create($request->validated());

            return redirect()
                ->route('bank-accounts.show', $account)
                ->with('success', 'Conta bancária criada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar conta bancária: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load('transactions');

        return Inertia::render('financial/bank-accounts/Show', [
            'account' => $bankAccount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        return Inertia::render('financial/bank-accounts/Edit', [
            'account' => $bankAccount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount)
    {
        try {
            $bankAccount->update($request->validated());

            return redirect()
                ->route('bank-accounts.show', $bankAccount)
                ->with('success', 'Conta bancária atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar conta bancária: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        try {
            // Check if account has transactions
            if ($bankAccount->transactions()->count() > 0) {
                return back()->with('error', 'Não é possível eliminar uma conta com transações associadas.');
            }

            $bankAccount->delete();

            return redirect()
                ->route('bank-accounts.index')
                ->with('success', 'Conta bancária eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar conta bancária: ' . $e->getMessage());
        }
    }
}
