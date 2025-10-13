<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierInvoiceRequest;
use App\Http\Requests\UpdateSupplierInvoiceRequest;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Financial\Invoice\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SupplierInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = SupplierInvoice::query()
            ->with(['supplier', 'supplierOrder'])
            ->filter($request->only(['search', 'status', 'supplier_id']))
            ->orderBy('invoice_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('financial/supplier-invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status', 'supplier_id']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('financial/supplier-invoices/Create', [
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name', 'email']),
            'supplierOrders' => SupplierOrder::with('supplier')->orderBy('number', 'desc')->get(['id', 'number', 'supplier_id', 'total_amount']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierInvoiceRequest $request)
    {
        $validated = $request->validated();

        \Log::info('🔍 [SUPPLIER INVOICE STORE] Dados validados:', $validated);

        try {
            $invoice = DB::transaction(function () use ($validated, $request) {
                \Log::info('📦 [SUPPLIER INVOICE STORE] Iniciando transação...');

                // Upload document if provided
                $documentPath = null;
                if ($request->hasFile('document')) {
                    \Log::info('📄 Uploading document...');
                    $documentPath = $request->file('document')->store('invoices/supplier/documents');
                    \Log::info('✅ Document uploaded:', ['path' => $documentPath]);
                }

                // Upload payment proof if provided
                $paymentProofPath = null;
                if ($request->hasFile('payment_proof')) {
                    \Log::info('💳 Uploading payment proof...');
                    $paymentProofPath = $request->file('payment_proof')->store('invoices/supplier/payment-proofs');
                    \Log::info('✅ Payment proof uploaded:', ['path' => $paymentProofPath]);
                }

                // Create invoice
                $invoiceData = [
                    'number' => SupplierInvoice::nextNumber(),
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $validated['due_date'],
                    'supplier_id' => $validated['supplier_id'],
                    'supplier_order_id' => $validated['supplier_order_id'] ?? null,
                    'total_amount' => $validated['total_amount'],
                    'document_path' => $documentPath,
                    'payment_proof_path' => $paymentProofPath,
                    'status' => $validated['status'],
                ];

                \Log::info('💾 Criando invoice com dados:', $invoiceData);
                $invoice = SupplierInvoice::create($invoiceData);
                \Log::info('✅ Invoice criada:', ['id' => $invoice->id, 'number' => $invoice->number]);

                // Send email if requested and status is paid
                if ($validated['status'] === 'paid' && ($validated['send_email'] ?? false)) {
                    if ($paymentProofPath) {
                        \Log::info('✉️ Enviando email de comprovativo...');
                        $invoice->sendPaymentProofEmail();
                    }
                }

                return $invoice;
            });

            \Log::info('✅ [SUPPLIER INVOICE STORE] Fatura criada com sucesso!', ['invoice_id' => $invoice->id]);

            return redirect()
                ->route('supplier-invoices.show', $invoice)
                ->with('success', 'Fatura criada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('❌ [SUPPLIER INVOICE STORE] Erro ao criar fatura:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder.supplier']);

        return Inertia::render('financial/supplier-invoices/Show', [
            'invoice' => $supplierInvoice,
            'hasDocument' => $supplierInvoice->document_path && Storage::exists($supplierInvoice->document_path),
            'hasPaymentProof' => $supplierInvoice->payment_proof_path && Storage::exists($supplierInvoice->payment_proof_path),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        return Inertia::render('financial/supplier-invoices/Edit', [
            'invoice' => $supplierInvoice,
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name', 'email']),
            'supplierOrders' => SupplierOrder::with('supplier')->orderBy('number', 'desc')->get(['id', 'number', 'supplier_id', 'total_amount']),
            'hasDocument' => $supplierInvoice->document_path && Storage::exists($supplierInvoice->document_path),
            'hasPaymentProof' => $supplierInvoice->payment_proof_path && Storage::exists($supplierInvoice->payment_proof_path),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierInvoiceRequest $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validated();

        // Check if status changed to paid
        $statusChangedToPaid = $supplierInvoice->status !== 'paid' && $validated['status'] === 'paid';

        try {
            DB::transaction(function () use ($validated, $request, $supplierInvoice, $statusChangedToPaid) {
                // Upload new document if provided
                if ($request->hasFile('document')) {
                    // Delete old document
                    if ($supplierInvoice->document_path) {
                        Storage::delete($supplierInvoice->document_path);
                    }
                    $validated['document_path'] = $request->file('document')->store('invoices/supplier/documents');
                } else {
                    $validated['document_path'] = $supplierInvoice->document_path;
                }

                // Upload new payment proof if provided
                if ($request->hasFile('payment_proof')) {
                    // Delete old payment proof
                    if ($supplierInvoice->payment_proof_path) {
                        Storage::delete($supplierInvoice->payment_proof_path);
                    }
                    $validated['payment_proof_path'] = $request->file('payment_proof')->store('invoices/supplier/payment-proofs');
                } else {
                    $validated['payment_proof_path'] = $supplierInvoice->payment_proof_path;
                }

                // Update invoice
                $supplierInvoice->update($validated);

                // Send email if status changed to paid and send_email is true
                if ($statusChangedToPaid && ($validated['send_email'] ?? false)) {
                    if ($validated['payment_proof_path']) {
                        $supplierInvoice->sendPaymentProofEmail();
                    }
                }
            });

            return redirect()
                ->route('supplier-invoices.show', $supplierInvoice)
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
    public function destroy(SupplierInvoice $supplierInvoice)
    {
        try {
            // Delete files
            if ($supplierInvoice->document_path) {
                Storage::delete($supplierInvoice->document_path);
            }
            if ($supplierInvoice->payment_proof_path) {
                Storage::delete($supplierInvoice->payment_proof_path);
            }

            $supplierInvoice->delete();

            return redirect()
                ->route('supplier-invoices.index')
                ->with('success', 'Fatura eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar fatura: ' . $e->getMessage());
        }
    }

    public function downloadDocument(SupplierInvoice $supplierInvoice)
    {
        if (!$supplierInvoice->document_path || !Storage::exists($supplierInvoice->document_path)) {
            return back()->with('error', 'Documento não encontrado.');
        }

        return Storage::download($supplierInvoice->document_path);
    }


    public function downloadPaymentProof(SupplierInvoice $supplierInvoice)
    {
        if (!$supplierInvoice->payment_proof_path || !Storage::exists($supplierInvoice->payment_proof_path)) {
            return back()->with('error', 'Comprovativo não encontrado.');
        }

        return Storage::download($supplierInvoice->payment_proof_path);
    }


    public function sendPaymentProof(SupplierInvoice $supplierInvoice)
    {
        try {
            if (!$supplierInvoice->payment_proof_path) {
                return back()->with('error', 'Fatura não tem comprovativo de pagamento.');
            }

            $sent = $supplierInvoice->sendPaymentProofEmail();

            if ($sent) {
                return back()->with('success', 'Email enviado com sucesso!');
            } else {
                return back()->with('error', 'Fornecedor não tem email configurado.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }
}
