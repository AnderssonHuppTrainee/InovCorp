<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Catalog\ContactRole;
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
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar proposta: ' . $e->getMessage());
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
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar proposta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        try {
            $proposal->delete();

            return redirect()
                ->route('proposals.index')
                ->with('success', 'Proposta eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar proposta: ' . $e->getMessage());
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
            return back()->with('error', 'Erro ao converter proposta: ' . $e->getMessage());
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
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }
}
