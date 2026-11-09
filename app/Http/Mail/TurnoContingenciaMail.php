<?php

namespace App\Http\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TurnoContingenciaMail extends Mailable
{
    // Traits de Laravel para permitir el encolado de correos en background y la serialización correcta de modelos Eloquent
    use Queueable, SerializesModels;

    /**
     * Propiedad pública que almacena la instancia del turno afectado. 
     * Al ser pública, Laravel la inyecta automáticamente y la hace accesible de forma directa en la vista Blade vinculada.
     * 
     * @var mixed
     */
    public $turno;

    /**
     * Constructor de la clase Mailable.
     * Recibe por inyección los datos o el modelo del turno afectado por la contingencia 
     * y los asigna a la propiedad de la clase.
     *
     * @param mixed $turno
     */
    public function __construct($turno)
    {
        $this->turno = $turno;
    }

    /**
     * Construye y configura los parámetros del correo electrónico.
     * Define la línea de asunto (subject) y la plantilla de vista Blade ('emails.turno_contingencia') 
     * que se renderizará para enviar al cliente.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Actualización sobre tu turno - Inconveniente de fuerza mayor')
                    ->view('emails.turno_contingencia'); // Vista Blade encargada del diseño del correo de contingencia
    }
}