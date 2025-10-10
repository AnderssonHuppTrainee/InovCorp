<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerInvoiceRequest;
use App\Http\Requests\UpdateCustomerInvoiceRequest;
use App\Models\Core\Entity;
use App\Models\Core\Order\Order;
use App\Models\Financial\Invoice\CustomerInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = CustomerInvoice::query()
            ->with(['customer', 'order'])
            ->filter($request->only(['search', 'status', 'customer_id']))
            ->orderBy('invoice_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate totals by status
        $totals = [
            'total' => CustomerInvoice::sum('total_amount'),
            'paid' => CustomerInvoice::where('status', 'paid')->sum('total_amount'),
            'pending' => CustomerInvoice::whereIn('status', ['sent', 'partially_paid'])->sum('balance'),
            'overdue' => CustomerInvoice::overdue()->sum('balance'),
        ];

        return Inertia::render('financial/customer-invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status', 'customer_id']),
            'customers' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'totals' => $totals,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('financial/customer-invoices/Create', [
            'customers' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'orders' => Order::with('client')->where('status', 'closed')->orderBy('number', 'desc')->get(['id', 'number', 'client_id', 'total_amount']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerInvoiceRequest $request)
    {
        $validated = $request->validated();

        try {
            $invoice = DB::transaction(function () use ($validated) {
                $invoiceData = [
                    'number' => CustomerInvoice::nextNumber(),
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $validated['due_date'],
                    'customer_id' => $validated['customer_id'],
                    'order_id' => $validated['order_id'] ?? null,
                    'total_amount' => $validated['total_amount'],
                    'paid_amount' => 0,
                    'balance' => $validated['total_amount'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => $validated['status'],
                ];

                return CustomerInvoice::create($invoiceData);
            });

            return redirect()
                ->route('customer-invoices.show', $invoice)
                ->with('success', 'Fatura de cliente criada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerInvoice $customerInvoice)
    {
        $customerInvoice->load(['customer', 'order.client']);

        return Inertia::render('financial/customer-invoices/Show', [
            'invoice' => $customerInvoice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerInvoice $customerInvoice)
    {
        $customerInvoice->load(['customer', 'order']);

        return Inertia::render('financial/customer-invoices/Edit', [
            'invoice' => $customerInvoice,
            'customers' => Entity::clients()->active()->orderBy('name')->get(['id', 'name']),
            'orders' => Order::with('client')->where('status', 'closed')->orderBy('number', 'desc')->get(['id', 'number', 'client_id', 'total_amount']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerInvoiceRequest $request, CustomerInvoice $customerInvoice)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $customerInvoice) {
                $customerInvoice->update($validated);

                // Recalculate balance
                $customerInvoice->balance = $customerInvoice->total_amount - ($validated['paid_amount'] ?? $customerInvoice->paid_amount);
                $customerInvoice->updateStatus();
            });

            return redirect()
                ->route('customer-invoices.show', $customerInvoice)
                ->with('success', 'Fatura atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerInvoice $customerInvoice)
    {
        try {
            $customerInvoice->delete();

            return redirect()
                ->route('customer-invoices.index')
                ->with('success', 'Fatura eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Register a payment for the invoice.
     */
    public function registerPayment(Request $request, CustomerInvoice $customerInvoice)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $customerInvoice->balance],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            DB::transaction(function () use ($request, $customerInvoice) {
                $customerInvoice->registerPayment($request->amount);
            });

            return back()->with('success', 'Pagamento registado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao registar pagamento: ' . $e->getMessage());
        }
    }
}




