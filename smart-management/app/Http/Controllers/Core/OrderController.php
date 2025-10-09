<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Core\Article;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['client', 'proposal'])
            ->filter($request->only(['search', 'status', 'client_id']))
            ->orderBy('order_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'client_id']),
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('orders/Create', [
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('status', 'active')->orderBy('name')->get(['id', 'reference', 'name', 'price', 'tax_rate_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        try {
            $order = DB::transaction(function () use ($validated) {
                // Criar encomenda
                $orderData = [
                    'number' => Order::nextNumber(),
                    'order_date' => $validated['order_date'],
                    'client_id' => $validated['client_id'],
                    'delivery_date' => $validated['delivery_date'] ?? null,
                    'proposal_id' => $validated['proposal_id'] ?? null,
                    'status' => $validated['status'],
                    'total_amount' => 0,
                ];

                $order = Order::create($orderData);

                // Criar itens
                foreach ($validated['items'] as $itemData) {
                    $order->items()->create($itemData);
                }

                // Calcular total
                $order->calculateTotal();

                return $order;
            });

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Encomenda criada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar encomenda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['client', 'proposal', 'items.article', 'items.supplier', 'supplierOrders.supplier']);

        return Inertia::render('orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['client', 'proposal', 'items.article', 'items.supplier']);

        return Inertia::render('orders/Edit', [
            'order' => $order,
            'clients' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('status', 'active')->orderBy('name')->get(['id', 'reference', 'name', 'price', 'tax_rate_id']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $order) {
                // Atualizar encomenda
                $order->update([
                    'order_date' => $validated['order_date'],
                    'client_id' => $validated['client_id'],
                    'delivery_date' => $validated['delivery_date'] ?? null,
                    'status' => $validated['status'],
                ]);

                // Remover itens antigos
                $order->items()->delete();

                // Criar novos itens
                foreach ($validated['items'] as $itemData) {
                    $order->items()->create($itemData);
                }

                // Recalcular total
                $order->calculateTotal();
            });

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Encomenda atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar encomenda: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Encomenda eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar encomenda: ' . $e->getMessage());
        }
    }

    /**
     * Convert order to supplier orders (group by supplier).
     */
    public function convertToSupplierOrders(Order $order)
    {
        try {
            if ($order->status !== 'closed') {
                return back()->with('error', 'Apenas encomendas fechadas podem ser convertidas.');
            }

            $supplierOrders = $order->convertToSupplierOrders();

            $count = count($supplierOrders);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', "Criadas {$count} encomenda(s) de fornecedor com sucesso!");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao converter encomenda: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for order.
     */
    public function generatePdf(Order $order)
    {
        try {
            $order->load(['client', 'items.article', 'items.supplier', 'proposal']);

            $pdf = Pdf::loadView('pdf.order', ['order' => $order]);

            $filename = "encomenda-{$order->number}.pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }
}
