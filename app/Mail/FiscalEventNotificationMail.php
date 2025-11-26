<?php

namespace App\Mail;

use App\Models\FiscalEvent;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FiscalEventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fiscalEvent;
    public $tenant;
    public $daysUntil;

    /**
     * Create a new message instance.
     */
    public function __construct(FiscalEvent $fiscalEvent, Tenant $tenant)
    {
        $this->fiscalEvent = $fiscalEvent;
        $this->tenant = $tenant;
        $this->daysUntil = now()->diffInDays($fiscalEvent->event_date, false);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $emoji = $this->daysUntil <= 3 ? '🚨' : '📅';
        $urgency = $this->daysUntil <= 3 ? 'URGENTE' : 'Recordatorio';

        return new Envelope(
            subject: "{$emoji} {$urgency}: {$this->fiscalEvent->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fiscal-event-notification',
            with: [
                'eventTitle' => $this->fiscalEvent->title,
                'eventDescription' => $this->fiscalEvent->description,
                'eventDate' => $this->fiscalEvent->event_date->format('d/m/Y'),
                'eventType' => $this->fiscalEvent->event_type_name,
                'daysUntil' => $this->daysUntil,
                'tenantName' => $this->tenant->name,
                'countryCode' => $this->fiscalEvent->country_code,
                'isUrgent' => $this->daysUntil <= 3,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
