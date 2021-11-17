<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use phpDocumentor\Reflection\Types\This;

class ContactWebEmail extends Mailable{

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $nombre, $empresa, $telefono, $ruc, $correo, $mensaje;

    public function __construct($nombre, $empresa, $telefono, $ruc, $correo, $mensaje){
        $this->nombre = $nombre;
        $this->empresa = $empresa;
        $this->telefono = $telefono;
        $this->ruc = $ruc;
        $this->correo = $correo;
        $this->mensaje = $mensaje;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->subject('Nuevo contacto Web')->from(config('mail.username'), 'Nuevo Contacto Web')->view('web.email');
    }
}
