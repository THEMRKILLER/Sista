<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use JWTAuth;
use URL;
class UsuarioController extends Controller
{
	 public function getUsers()
    {
        return view('sysadmin.home')->with('users',User::all());
    }

    public function showForm()
    {
    	return view('sysadmin.registeruser');
    }
    public function registrar(Request $request)
    {
    	$validator = $this->validar($request->all());
    	if($validator->fails())
    	{
    		return redirect('sysadmin/alta_usuario')
                        ->withErrors($validator)
                        ->withInput();
    	}

    }
    private function validar($data)
    {
    	 return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:sysadmins',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    public function getPerfilInfo(Request $request)
    {
        $user_id = $request->get('user_id');

        return User::userInfo($user_id);

    }
    public function logout2()
    {

        JWTAuth::invalidate(JWTAuth::getToken());

    }

    public function updateAvatar(Request $request)
    {
        $data = $request->get('avatar');

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        $folderName =  storage_path('app').'/profile_images/';
        $safeName = str_random(10). uniqid().time()  .'.'.'png';

        file_put_contents($folderName.$safeName, $data);

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        if($user->avatar != null)
        {
        $file_name_actual = explode('/', $user->avatar);
        try{
        if(file_exists($folderName.$file_name_actual[count($file_name_actual)-1]))
            unlink($folderName.$file_name_actual[count($file_name_actual)-1]);
        }
        catch(\Exception $e){}
        }
        $url = URL::asset('api/v1/foto_perfil/'.$safeName);
        $user->avatar = $url;
        $user->save();
        

        return response()->json(['success' => true,'avatar' => $user->avatar ],200);


    }
    public function getProfilePicture($image_name)
    {

    $pathToFile = storage_path('app/profile_images/'.$image_name);;
    if(file_exists($pathToFile))return response()->file($pathToFile);

    else response()->json(['message' => 'Foto de perfil no encontrado'],404);

    }

}
