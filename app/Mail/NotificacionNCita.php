<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\cita;
class NotificacionNCita extends Mailable
{
    public $cita;
    public $medico;
    public $opcionMensaje;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(cita $cita,$medico,$opcionMensaje) 
    {
         $this->cita=$cita;
         $this->medico=$medico;
         $this->opcionMensaje=$opcionMensaje;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.MensajeNCita');
    }
}
