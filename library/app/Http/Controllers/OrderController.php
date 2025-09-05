<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{


    public function index(Request $request)
    {

        $query = Order::with(['user']);

        if ($request->search) {
            $search = $request->search;
            $query->WhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }


    public function show(Order $order, OrderItem $orderItem)
    {
        //verificação

        $order->load(['user', 'items.book']); // carrega todos os itens da ordem com os livros
        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $data = [
            'invoice' => $order,
            'customer' => $order->user,
            'items' => $order->items,
            'total' => $order->total,
        ];

        $pdf = Pdf::loadView('invoices.invoice', $data);

        return $pdf->stream("nota-fiscal-{$order->id}.pdf");
    }
}
