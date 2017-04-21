<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\ResetPassword;
use URL;
use Carbon\Carbon;
use DB;
class RestablecerPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $usuario;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = $this->generarToken();
        return $this->view('emails.RestablecerPassword')->with('user',$this->usuario)->with('url',$url);
    }

    public function generarToken()
    {
        $email = $this->usuario->email;
        $token = bin2hex(random_bytes(16));
       $datas =  ['email' => $email, 'token' => $token, 'created_at' => Carbon::now()];
        DB::table('password_resets')->insert($datas);
        return "https://www.sistacliente.herokuapp.com/sistema/passwordreset/".$token;
    }
}
