<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class MonthlyBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $monthName;
    public $year;
    public $excelPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant, string $monthName, int $year, string $excelPath)
    {
        $this->tenant = $tenant;
        $this->monthName = $monthName;
        $this->year = $year;
        $this->excelPath = $excelPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📦 Backup Mensual Dataflow - {$this->monthName} {$this->year}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-backup',
            with: [
                'tenantName' => $this->tenant->name,
                'monthName' => $this->monthName,
                'year' => $this->year,
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
        return [
            Attachment::fromPath($this->excelPath)
                ->as("backup_dataflow_{$this->monthName}_{$this->year}.xlsx")
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
