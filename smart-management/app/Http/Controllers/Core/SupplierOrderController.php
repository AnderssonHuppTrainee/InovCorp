<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierOrderRequest;
use App\Http\Requests\UpdateSupplierOrderRequest;
use App\Models\Core\Article;
use App\Models\Core\Entity;
use App\Models\Core\Order\SupplierOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierOrder::query()->with(['supplier', 'order']);


        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $supplierOrders = $query->orderBy('order_date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('supplier-orders/Index', [
            'supplierOrdersData' => $supplierOrders,
            'filters' => $request->only(['search', 'status', 'supplier_id']),
            'suppliers' => Entity::suppliers()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load(['supplier', 'order.items.article', 'invoices']);

        return Inertia::render('supplier-orders/Show', [
            'supplierOrder' => $supplierOrder,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierOrder $supplierOrder)
    {
        try {
            if ($supplierOrder->invoices()->exists()) {
                return back()->with('error', 'Não é possível eliminar esta encomenda, pois existem faturas associadas.');
            }

            $supplierOrder->delete();

            return redirect()
                ->route('supplier-orders.index')
                ->with('success', 'Encomenda de fornecedor eliminada com sucesso!');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Esta encomenda não pode ser eliminada pois está associada a outros registos (faturas, etc).');
            }

            return back()->with('error', 'Erro ao eliminar encomenda. Por favor, tente novamente.');

        } catch (\Exception $e) {
            \Log::error('Erro ao eliminar encomenda de fornecedor:', [
                'supplier_order_id' => $supplierOrder->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro inesperado ao eliminar encomenda. Contacte o suporte.');
        }
    }
}



