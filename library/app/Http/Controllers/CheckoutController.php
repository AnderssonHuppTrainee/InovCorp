<?php

namespace App\Http\Controllers;
use App\Jobs\SendInvoiceJob;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = Auth::user()->cart()->with('items.book')->firstOrFail();

        return view('checkout.index', compact('cart'));

    }

    public function saveAddress(Request $request, Order $order)
    {
        $request->validate([
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:50',
        ]);
        $cart = Auth::user()->cart()->with('items.book')->firstOrFail();

        $total = $cart->items->sum(fn($item) => $item->quantity * $item->price);

        //verifica se ja existre uma ordem pendente p evitar duplicacao na bd
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,

            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
            session(['checkout_order_id' => $order->id]);
        } else {
            // se ja existe, apenas atualiza 
            $order->update([
                'total' => $total,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));//pega a key


        $paymentIntent = PaymentIntent::create([
            'amount' => (int) round($order->total * 100), // centavos
            'currency' => 'eur',
            'metadata' => [
                'order_id' => $order->id
            ],
        ]);
        //cria O paymentIntent ID 
        $order->update(['payment_intent_id' => $paymentIntent->id]);


        return response()->json(
            [
                'success' => true,
                'order_id' => $order->id,
                'total' => $total,
                'client_secret' => $paymentIntent->client_secret,
                'stripe_key' => config('services.stripe.key')
            ]
        );
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        // atualizar status apenas se ainda n estiver pago
        if ($order->status !== 'paid') {
            $order->update(['status' => 'paid']);
        }
        //limpar o carrinho
        $cart = Auth::user()->cart()->with('items.book')->firstOrFail();
        $cart->items()->delete();

        SendInvoiceJob::dispatch($order, auth()->user());


        return view('checkout.success', compact('order'));
    }
}
