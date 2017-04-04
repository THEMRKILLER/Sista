<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\calendario;
use Validator;
use JWTAuth;
use URL;
use Redirect;
use Hash;
use Illuminate\Support\Facades\Storage;
class UsuarioController extends Controller
{

    public function index()
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $calendario_id = $user->calendario->id;
        return response()->json(['user' => $user,'calendario_id' => $calendario_id],200);
    }
	 public function getUsers()
    {
        return view('sysadmin.home')->with('users',User::all());
    }

     public function altausuario(Request $request)
    {
        $data = $request->all();
        $validator = $this->validar_user($data);
        if($validator->fails())
        {
            return redirect('sysadmin/altausuario')
                        ->withErrors($validator)
                        ->withInput();
        }

         
         $user = new User();
         $user->name = $data['name'];
         $user->email = $data['email'];
         $user->password = bcrypt($data['password']);
         $user->extra->dominio = $data['dominio'];
         $user->extra->completo = false;
         $user->avatar = 'default.png';
         $user->informacion_profesional_resumen = "";
         $user->informacion_profesional_completo = "";
         $user->cedula_profesional = $data['cedula'];
         $user->save();
         $calendario = new calendario();
         $user->calendario()->save($calendario);

           return redirect()->route('syshome');



    }
    public function showForm()
    {
    	return view('sysadmin.registeruser');
    }
    public function registrar(Request $request)
    {
    	////ssl.gstatic.com/accounts/ui/avatar_2x.png

    }
    private function validar($data)
    {
    	 return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:sysadmins',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    private function validar_user(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'dominio' => 'required|url',
            'cedula' => 'required'
        ]);
    }

    public function getPerfilInfo(Request $request)
    {
        $user_id = $request->get('user_id');

        return User::userInfo($user_id);

    }
    public function getCv(Request $request)
    {
        $calendario_id = $request->get('calendario_id');
        return User::userCVInfo($calendario_id);
    }
    public function logout2()
    {

      //  JWTAuth::invalidate(JWTAuth::getToken());

    }

    public function updateAvatar(Request $request)
    {
        $data = $request->get('avatar');

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        $safeName = str_random(10). uniqid().time()  .'.'.'png';

        Storage::disk('s3')->put('avatars/' . $safeName, $data);


        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        if($user->avatar != null)
        {
        $file_name_actual = explode('/', $user->avatar);
        $file_name_actual = $file_name_actual[count($file_name_actual)-1];
        try{

        if($file_name_actual != 'default.png')
            {
                $exists = Storage::disk('s3')->exists('avatar/'.$file_name_actual);

                if($exists)Storage::disk('s3')->delete('avatar/'.$file_name_actual);
                
            }
        }
        catch(\Exception $e){}
        }

        $url = Storage::disk('s3')->url('avatars/' . $safeName);
        $user->avatar = $url;
        $user->save();
        
        return response()->json(['success' => true,'avatar' => $url],200);


    }
    public function getProfilePicture($image_name)
    {

    $pathToFile = storage_path('app/profile_images/'.$image_name);

    if(file_exists($pathToFile))return response()->file($pathToFile);

    else response()->json(['message' => 'Foto de perfil no encontrado'],404);

    }

    public function update(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $validator = Validator::make($request->all(), [
                // Here's how our new validation rule is used.
                'nombre' => 'required',
                'cedula_profesional' => 'required',
                'informacion_profesional_resumen' => 'required|max:255|string',
                'informacion_profesional_completo' => 'required|string|max:65535'
        ]);

        if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
        
            }

        $user->name = $request->get('nombre');
        $user->cedula_profesional = $request->get('cedula_profesional');
        $user->informacion_profesional_resumen = $request->get('informacion_profesional_resumen');
        $user->informacion_profesional_completo = $request->get('informacion_profesional_completo');
        $user->save();
        return response()->json(['user' => $user],200);



    }
     public function settingsUpdatePassword(Request $request)
    {

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);


         
    $validator = Validator::make($request->all(), [
    // Here's how our new validation rule is used.
        'current-password' => 'required',
        'newpassword' => 'required|min:6|confirmed',
    ]);

        if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
        
            }

        if(Hash::check($request->get('current-password'), $user->password))
        {
            $user->password = Hash::make($request->get('newpassword'));
            $user->save();
            return response()->json(['success' => true],200);
        }
        else{
            return response()->json(array(
                                            'success' => false,
                                            'errors' => ['La contraseña actual ingresada es incorrecta']
                                            ), 
                            400); // 400 being the HTTP code for an invalid request.
        }


       

        

    }

    public function completar_registro(Request $request)
    {
        $calendario_id = $request->get('calendario');
        $calendario = calendario::find($calendario_id);
        if(!$calendario) return "El calendario no existe";
        $user = $calendario->user;
        $user->MailAndChangePassword();
        $user->extra->completo = true;
        return redirect()->route('syshome');


    }

    
}
