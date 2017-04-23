<?php

namespace App\Http\Controllers;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Request;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

            try {
                
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

            $user = JWTAuth::toUser($token);
            return response()->json(['token'  => compact('token') , 'user' => $user,'calendario_id' => $user->calendario->id],200);
    }

    public function invalidar_token(Request $request)
    {
        if( ! $request->has('token_old') )
            return response()->json(['esto' => $request->get('token_old')],403);

        $token = JWTAuth::getToken();
        $nuevo_usuario = JWTAuth::toUser($token);
      //  JWTAuth::invalidate($request->get('token_old'));
        response()->json(null,200);
        
    }

}
