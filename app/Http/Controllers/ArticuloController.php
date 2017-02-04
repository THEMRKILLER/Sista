<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use App\Articulo;
use Image;
class ArticuloController extends Controller
{
    public function store(Request $request)
    {
    	$rules = array(
            'titulo' => 'required|max:255',
            'resumen' => 'required',
            'caratula' => 'required|max:20000|image',
            'contenido' => 'required|string|max:65535'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ), 
                                400); // 400 being the HTTP code for an invalid request.
        
            }




       $token = JWTAuth::getToken();
       $user = JWTAuth::toUser($token);

       try {
       $caratula_path = $request->file('caratula')->store('images');

       } catch (Exception $e) {
       	return response()->json(null,500);
       	
       }

       $articulo = new Articulo();
       $articulo->titulo = $request->get('titulo');
       $articulo->resumen = $request->get('resumen');
       $articulo->caratula = $caratula_path;
       $articulo->contenido = $request->get('contenido');

       $user->articulos()->save($articulo);
       return response()->json(['id' => $articulo->id],200);

    }

    public function getImage($image_name)
    {
    	$pathToFile = storage_path('app/images/'.$image_name);
      
    	return response()->file($pathToFile);

    }

    public function getArticulo($id)
    {
    	$articulo = Articulo::find($id);

    	if($articulo == null) return response()->json(['error' => true],404);


    	$autor = $articulo->user()->get(['name','avatar'])->first();


      $articulos_models = $articulo->user->articulos()->get(['id']);
      $articulos_arr = array();
      foreach ($articulos_models as $articulo_m) array_push($articulos_arr, $articulo_m->id);

    	return response()->json(['articulo' => $articulo ,'articulos' => $articulos_arr ,'autor' => $autor],200);
    }
    public function getArticulos()
    {
    	   $token = JWTAuth::getToken();
       	 $user = JWTAuth::toUser($token);
       	 $articulos = $user->articulos;
         $articulos_arr = array();
         foreach ($articulos as $articulo)
         {
          $articulo->caratula =  url('api/v1/'.$articulo->caratula);
          array_push($articulos_arr,['caratula' => $articulo->caratula ,'id' => $articulo->id, 'resumen' => $articulo->resumen,
                                     'titulo' => $articulo->titulo, 'autor' => $articulo->user->name,
                                     'fecha' => $articulo->updated_at
                                    ]);
         }

       	 return response()->json($articulos_arr,200);

    }
}
