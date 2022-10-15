<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class MyTestMail extends Mailable
{
    public $details;
    use Queueable, SerializesModels;
    public $workOrder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $details)
    {
        $this->details= $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Creacion de usuario')
        ->markdown('creacionUsuario');

    return $this;

    }
}
