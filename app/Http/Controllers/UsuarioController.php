<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
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
    	if()
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
}
