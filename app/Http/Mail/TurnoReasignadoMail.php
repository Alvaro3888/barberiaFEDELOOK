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
    // Traits de Laravel para permitir encolar el correo en background y serializar modelos Eloquent de forma segura
    use Queueable, SerializesModels;

    /**
     * Propiedades públicas que almacenan el turno afectado y la información del nuevo barbero asignado.
     * Al ser públicas, Laravel las inyecta de forma automática y quedan accesibles directamente en la vista Blade.
     */
    public $turno;
    public $nuevoBarbero;

    /**
     * Constructor de la clase Mailable.
     * Recibe por inyección las instancias del turno modificado y del nuevo barbero 
     * y las asigna a las propiedades de la clase.
     *
     * @param mixed $turno
     * @param mixed $nuevoBarbero
     */
    public function __construct($turno, $nuevoBarbero)
    {
        $this->turno = $turno;
        $this->nuevoBarbero = $nuevoBarbero;
    }

    /**
     * Define los metadatos del mensaje de correo (como el asunto / subject).
     * Utiliza la sintaxis moderna de sobres (Envelope) introducida en Laravel.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización en tu Turno - Cambio de Profesional',
        );
    }

    /**
     * Define la definición del contenido del correo, especificando la vista Blade 
     * encargada del diseño y estructura visual que recibirá el cliente ('emails.turno_reasignado').
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.turno_reasignado',
        );
    }

    /**
     * Obtiene los archivos adjuntos para el mensaje de correo.
     * En este caso retorna un array vacío ya que la notificación es meramente informativa.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}