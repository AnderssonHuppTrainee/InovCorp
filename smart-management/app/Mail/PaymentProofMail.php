<?php

namespace App\Mail;

use App\Models\Financial\Invoice\SupplierInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentProofMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public SupplierInvoice $invoice
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Comprovativo de Pagamento - Fatura {$this->invoice->number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-proof',
            with: [
                'invoice' => $this->invoice,
                'supplier' => $this->invoice->supplier,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Anexar comprovativo de pagamento se existir
        if ($this->invoice->payment_proof_path && Storage::exists($this->invoice->payment_proof_path)) {
            $attachments[] = Attachment::fromStorage($this->invoice->payment_proof_path)
                ->as('comprovativo-pagamento-' . $this->invoice->number . '.pdf');
        }

        return $attachments;
    }
}
