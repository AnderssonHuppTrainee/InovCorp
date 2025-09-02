<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function addressForm()
    {
        $cart = Auth::user()->cart()->with('items.book')->firstOrFail();
        return view('checkout.address', compact('cart'));
    }


    public function storeAddress(Request $request)
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

        //criar a ordem de encomeneda
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
        // transfere os itens
        foreach ($cart->items as $item) {
            $order->items()->create([
                'book_id' => $item->book_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        //  limpar o carrinho
        $cart->items()->delete();

        return redirect()->route('checkout.payment', $order);
    }


    public function payment(Order $order)
    {
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
        return view('checkout.payment', [
            'order' => $order,
            'clientSecret' => $paymentIntent->client_secret,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }


}
