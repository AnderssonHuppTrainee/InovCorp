<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Core\Article;
use App\Models\Core\Entity;
use App\Models\Core\Proposal\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $proposals = Proposal::query()
            ->with(['client'])
            ->filter($request->only(['search', 'status', 'client_id']))
            ->orderBy('proposal_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('proposals/Index', [
            'proposals' => $proposals,
            'filters' => $request->only(['search', 'status', 'client_id']),
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('proposals/Create', [
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('status', 'active')->orderBy('name')->get(['id', 'reference', 'name', 'price', 'tax_rate_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProposalRequest $request)
    {
        $validated = $request->validated();

        try {
            $proposal = DB::transaction(function () use ($validated) {
                // Criar proposta
                $proposalData = [
                    'number' => Proposal::nextNumber(),
                    'proposal_date' => $validated['proposal_date'],
                    'client_id' => $validated['client_id'],
                    'validity_date' => $validated['validity_date'],
                    'status' => $validated['status'],
                    'total_amount' => 0,
                ];

                $proposal = Proposal::create($proposalData);

                // Criar itens
                foreach ($validated['items'] as $itemData) {
                    $proposal->items()->create($itemData);
                }

                // Calcular total
                $proposal->calculateTotal();

                return $proposal;
            });

            return redirect()
                ->route('proposals.show', $proposal)
                ->with('success', 'Proposta criada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'client_id')) {
                    return back()->withInput()->with('error', 'Cliente inválido ou inexistente.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao criar proposta. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar proposta:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao criar proposta. Contacte o suporte.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['client', 'items.article', 'items.supplier']);

        return Inertia::render('proposals/Show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal)
    {
        $proposal->load(['client', 'items.article', 'items.supplier']);

        return Inertia::render('proposals/Edit', [
            'proposal' => $proposal,
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('status', 'active')->orderBy('name')->get(['id', 'reference', 'name', 'price', 'tax_rate_id']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProposalRequest $request, Proposal $proposal)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $proposal) {
                // Atualizar proposta
                $proposal->update([
                    'proposal_date' => $validated['proposal_date'],
                    'client_id' => $validated['client_id'],
                    'validity_date' => $validated['validity_date'],
                    'status' => $validated['status'],
                ]);

                // Remover itens antigos
                $proposal->items()->delete();

                // Criar novos itens
                foreach ($validated['items'] as $itemData) {
                    $proposal->items()->create($itemData);
                }

                // Recalcular total
                $proposal->calculateTotal();
            });

            return redirect()
                ->route('proposals.show', $proposal)
                ->with('success', 'Proposta atualizada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                if (str_contains($e->getMessage(), 'client_id')) {
                    return back()->withInput()->with('error', 'Cliente inválido ou inexistente.');
                }
            }

            return back()->withInput()->with('error', 'Erro ao atualizar proposta. Por favor, verifique os dados.');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar proposta:', [
                'proposal_id' => $proposal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Erro inesperado ao atualizar proposta. Contacte o suporte.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        try {
            $proposalNumber = $proposal->number;
            $proposal->delete();

            return redirect()
                ->route('proposals.index')
                ->with('success', "Proposta \"{$proposalNumber}\" eliminada com sucesso!");

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta proposta não pode ser eliminada pois está associada a outros registos (encomendas, etc).');
            }

            return back()->with('error', 'Erro ao eliminar proposta. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar proposta:', [
                'proposal_id' => $proposal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar proposta. Contacte o suporte.');
        }
    }

    /**
     * Convert proposal to order.
     */
    public function convertToOrder(Proposal $proposal)
    {
        try {
            $order = $proposal->convertToOrder();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Proposta convertida em encomenda com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao converter proposta:', [
                'proposal_id' => $proposal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro ao converter proposta. Contacte o suporte.');
        }
    }

    /**
     * Generate PDF for proposal.
     */
    public function generatePdf(Proposal $proposal)
    {
        try {
            $proposal->load(['client', 'items.article']);

            $pdf = Pdf::loadView('pdf.proposal', ['proposal' => $proposal]);

            $filename = "proposta-{$proposal->number}.pdf";

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de proposta:', [
                'proposal_id' => $proposal->id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro ao gerar PDF. Por favor, tente novamente.');
        }
    }
}
