<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use App\Articulo;
use Image;
use Json;
use Exception;
class ArticuloController extends Controller
{
    /**
     * Guarda un nuevo articulo
     *    
     * @param  \Illuminate\Http\Request  $request  ['titulo','resumen','caratula','contenido']
     * @return \Illuminate\Http\Response 
     */
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

     /**
     * Actualiza un articulo mediante su id
     *  
     * @param  \Illuminate\Http\Request  $request  ['titulo','resumen','caratula','contenido','id','caratula_url']
     * caratula_url -> es la url de la última imagen que contenia el articulo, en caso de haber cargado
     * una nueva imagen esta vendrá como null 
     * @return \Illuminate\Http\Response [json -> responde con el id del articulo que se acaba de actualizar]
     */

    public function update(Request $request)
    {
          $rules = array(
            'titulo' => 'required|max:255',
            'resumen' => 'required',
            'caratula' => 'required_without:caratula_url|max:20000|image',
            'contenido' => 'required|string|max:65535',
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
      $articulo = Articulo::find($request->get('id'));

       try {
        if($request->hasFile('caratula')) {
          //el archivo es eliminado del servidor en caso de existir
          if(file_exists(storage_path('app/').$articulo->caratula))
            unlink(storage_path('app/').$articulo->caratula);
          
          $caratula_path = $request->file('caratula')->store('images');

        }
        else $caratula_path = $articulo->caratula;
       } catch (Exception $e) {
        return response()->json(null,500);
        
       }

       $articulo->titulo = $request->get('titulo');
       $articulo->resumen = $request->get('resumen');
       $articulo->caratula = $caratula_path;
       $articulo->contenido = $request->get('contenido');
       $user->articulos()->save($articulo);
       return response()->json(['id' => $articulo->id],200);

    }

     /**
     * Elimina un articulo mediante su id
     *  
     * @param  \Illuminate\Http\Request  $request  ['id']
     * @return \Illuminate\Http\Response 
     */
    public function delete(Request $request)
    {
      
        $articulo = Articulo::find($request->get('id'));
        if(!$articulo) return response()->json(['errors'=>["El articulo no fue encontrado"]],404);
        $articulo->delete();
        return response()->json(null,200);
      
      
    }

     /**
     * Obtiene el archivo imagen que corresponde al nombre recibido como parametro
     *  
     * @param  $image_name 
     * @return \Illuminate\Http\Response responde con un archivo imagen ó json con código de error, 
     * en caso de que la imagen no haya sido encontrada en el servidor
     */
    public function getImage($image_name)
    {
    	$pathToFile = storage_path('app/images/'.$image_name);
      if(file_exists($pathToFile))return response()->file($pathToFile);
      else return response()->json('errors'=> ['imagen' => 'la imagen solicitada no se encuentra en el servidor'],404);
    	
    }

    /**
     * Obtiene un articulo mediante su id
     *  
     * @param  $id
     * @return \Illuminate\Http\Response 
     */
    public function getArticulo($id)
    {
    	$articulo = Articulo::find($id);
      //en caso de no encontrar el articulo solicitado responde con un código de estado 404
    	if(!$articulo) return response()->json(['error' => true],404);

      //obtiene toda la información necesaria relacionada al autor del articulo que se solicita
    	$autor = $articulo->user()->get(['name','avatar','informacion_profesional_resumen'])->first();

      //obtiene los ids de los articulos redactados por el mismo autor
      $articulos_models = $articulo->user->articulos()->get(['id']);
      $articulos_arr = array();
      foreach ($articulos_models as $articulo_m) array_push($articulos_arr, $articulo_m->id);
      //modifica la url para tener acceso a la caratula a nivel de objeto (no lo guarda en la BD)
      $articulo->caratula  = url('api/v1/'.$articulo->caratula);
      $autor->avatar = url()->asset('api/v1/foto_perfil/'.$autor->avatar);
    	return response()->json(['articulo' => $articulo ,'articulos' => $articulos_arr ,'autor' => $autor],200);
    }
     /**
     * Obtiene una lista de articulos redactados por el autor quien hace la petición
     *  
     * @param  
     * @return \Illuminate\Http\Response 
     */
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
                                     'fecha' => $articulo->updated_at,

                                    ]);
         }

       	 return response()->json($articulos_arr,200);

    }
}
