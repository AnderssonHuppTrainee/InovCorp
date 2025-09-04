<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;
class StripeWebhookController extends Controller
{
    //

    public function handle(Request $request)
    {
        //requisicao enviada pela stripe
        $payload = $request->getContent();
        //signature p validacao
        $sigHeader = $request->header('Stripe-Signature');

        //endpoint para garantir q so a stripe assine events 
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        \Log::info('Webhook recebido', ['payload' => $payload, 'headers' => $sigHeader]);

        try {

            //construr o evento webhook
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400); //erro no payload
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);//error na signature
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':

                $pi = $event->data->object;
                //busca o pi na db orders
                $order = Order::where('payment_intent_id', $pi->id)->first();

                if ($order) {
                    $order->update(['status' => 'paid']); //passa a ordem para paga

                    //envio de fatura pro email
                    //gerir stock
                }
                break;

            case 'payment_intent.payment_failed':
                $pi = $event->data->object;

                $order = Order::where('payment_intent_id', $pi->id)->first();
                if ($order) {
                    $order->update(['status' => 'failed']);//falhou
                }
                break;
        }

        \Log::info('Stripe event received', ['type' => $event->type]);

        //retorna um json para o stripe
        return response()->json(['received' => true]);

    }
}
