<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Mail\PasswordChanged;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function calendario()
    {
              return $this->hasOne('App\calendario');

    }

    public function articulos()
    {
        return $this->hasMany('App\Articulo');
    }


    public static function userInfo($user_id)
    {
        
        $user = User::find($user_id);
        if($user)
        { 
            return response()->json([
            'nombre' => $user->name, 
            'correo_electronico' => $user->email,
            'cedula_profesional' => $user->cedula_profesional,
            'avatar' => $user->avatar,
            'informacion_profesional_completo' => $user->informacion_profesional_completo
            ],200);
        }
        else return response()->json(null,404);
    }
    public static function userCVInfo($calendario_id)
    {
        
        $calendario = calendario::find($calendario_id);
        if($calendario)
        { 
            $user = $calendario->user;
            
            return response()->json([
            'nombre' => $user->name, 
            'correo_electronico' => $user->email,
            'cedula_profesional' => $user->cedula_profesional,
            'avatar' => $user->avatar,
            'informacion_profesional_completo' => $user->informacion_profesional_completo
            ],200);
        }
        else return response()->json(null,404);
    }
    public static function generateNewPassword()
    {
         $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < 6; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);  
    }
    public function MailAndChangePassword()
    {
       $user= $this->first();
       
        $new_pass=$this->generateNewPassword();
        $user->password = bcrypt($new_pass);
         $destinatario=$user->email;
        \Mail::to($destinatario)->send(new PasswordChanged($user, $new_pass));
        return $user;
    }

}
