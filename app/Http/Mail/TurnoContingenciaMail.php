<?php

namespace App\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TurnoContingenciaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $turno;

    public function __construct($turno)
    {
        $this->turno = $turno;
    }

    public function build()
    {
        return $this->subject('Actualización sobre tu turno - Inconveniente de fuerza mayor')
                    ->view('emails.turno_contingencia'); // O la vista blade que prefieras usar
    }
}

