<?php

namespace App\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TurnoReasignadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $turno;
    public $nuevoBarbero;

    public function __construct($turno, $nuevoBarbero)
    {
        $this->turno = $turno;
        $this->nuevoBarbero = $nuevoBarbero;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización en tu Turno - Cambio de Profesional',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.turno_reasignado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}