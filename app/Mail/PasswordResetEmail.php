<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $userName;

    public function __construct(string $resetUrl, string $userName)
    {
        $this->resetUrl = $resetUrl;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Recuperación de Contraseña - Dataflow')
                    ->view('emails.password-reset')
                    ->with([
                        'resetUrl' => $this->resetUrl,
                        'userName' => $this->userName,
                    ]);
    }
}
