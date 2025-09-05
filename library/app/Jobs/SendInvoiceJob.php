<?php

namespace App\Jobs;
use App\Models\Order;
use App\Models\User;
use Exception;
use Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order, public User $user)
    {

        $this->order->load('user', 'items.book');

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'invoice' => $this->order,
            'customer' => $this->order->user,
            'items' => $this->order->items,
            'total' => $this->order->total
        ];
        $pdf = PDF::loadView('invoices.invoice', $data);

        try {
            Mail::to($this->user->email)->send(new InvoiceMail($this->order, $pdf));
        } catch (Exception $e) {
            \Log::error('Erro ao enviar email: ' . $e->getMessage());
            throw $e;
        }
        // marcar como invoice enviada
        //$this->order->update(['invoice_sent_at' => now()]);

    }
}
