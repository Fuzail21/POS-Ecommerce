<?php

namespace App\Mail;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Sale $order;
    public string $oldStatus;

    public function __construct(Sale $order, string $oldStatus)
    {
        $this->order     = $order;
        $this->oldStatus = $oldStatus;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order #' . $this->order->invoice_number . ' Status Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
