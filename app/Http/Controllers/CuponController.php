<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cupon;
use App\tipo;
use Validator;
use App\calendario;
use Carbon\Carbon;
use Exception;
use JWTAuth;
class CuponController extends Controller
{
     /**
     * Crea un nuevo cupon
     * @param \Illuminate\Http\Request  $request  ['servicio_id','porcentaje','fecha_inicial','fecha_final','word_key']
     * porcentaje -> entero del 0 - 100 que representa el porcentaje de descuento que se aplicará cuando se active el cupon al agendar una cita
     * fecha_inicial -> fecha donde comenzará se valido el cupon
     * fecha_final -> despues de esta fecha el cupón ya no esvalido
     * word_key -> palabra que usara para indexar e identificar el cupon
     * @return \Illuminate\Http\Response 
     */
    public function create(Request $request)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

    	$rules = array(
                        'porcentaje' => 'required|integer|max:100|min:0',
                        'fecha_inicial' => 'required|date_format:Y-m-d|before_or_equal:fecha_final',
                        'fecha_final' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicial'
                );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                                            'success' => false,
                                            'errors' => $validator->getMessageBag()->toArray()
                                            ),
                                400); // 400 being the HTTP code for an invalid request.
            }

            $servicio = $user->calendario->tipos()->where('id',$request->get('servicio_id'))->first();

            $codigo = $request->get('word_key');
            if(!$servicio) return response()->json(['errors' => 'El servicio no existe'],404);

            if($codigo == null || $codigo == '' || $codigo == ' ')
            {
                $codigo = $this->generarCodigo(null);
            }
            else {
                //hace una busqueda entre todos los cupones si ya existe un cupon con el mismo código
                $exist = Cupon::where('codigo',$codigo)->count() > 0 ? true : false ;
                if($exist) return response()->json(['errors' => ['Ya existe un cupon con el mismo código, escoja otro código']],404);
                
            }
           
           
           
            $cupon = new Cupon();
            $cupon->codigo = $codigo;
            $cupon->porcentaje = $request->get('porcentaje');
            $cupon->fecha_inicial = $request->get('fecha_inicial');
            $cupon->fecha_final = $request->get('fecha_final');
            $servicio->cupones()->save($cupon);

            return response()->json(['codigo' => $cupon->codigo],200);

    }

       /**
     * Devuelve una lista con todos los cupones y servicios del usuario quien hace la solicitud
     * @param \Illuminate\Http\Request  $request  ['calendario_id']
     * @return \Illuminate\Http\Response 
     */

    public function index(Request $request)
    {
        $calendario_id = $request->get('calendario_id');
        $calendario = calendario::find($calendario_id);
        if(!$calendario) return response()->json(['Recurso no encontrado'],404);
        $cupones = $calendario->cupones;
        $servicios = $calendario->tipos;

        return response()->json(['cupones' => $cupones,'servicios' => $servicios],200);  

    }
       /**
     * Genera un nuevo código para un cupón
     * @param $key
     * key -> palabra clave que identifique al cupón
     * @return String $codigo
     */
    public function generarCodigo($key)
    {
            $codigo  = null;
            $repeated = true;
            //numero se inicia en 3 que será la longitud de la cadena que se va concatenar
            $numero = 3; 
            //count (contador) que inica en 0 y cuenta las veces en que el código que el sistema genera ya existe
            // en otros registros
            $count = 0;        
            do{
                $count++;
                //si el contador llega a 100 la cantidad de longitud de la cadena que se concatena aumenta 1
                if($count > 100) {
                   $numero = $numero + 1;
                   $count = 0;    

                }
                //si el cliente no ha ingresado una palabra clave el sistema generará un id "único"
                if($key == '' || $key == null || $key == ' ' || $key == 'undefined') $codigo = uniqid();
                else $codigo = $key.rand(0, 10).substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $numero)), 0, $numero);          
            
                $repeated = Cupon::where('codigo',$codigo)->count() > 0;
            }
            while($repeated);   

            return $codigo;    
    }
   /**
     * Verifica si el código de cupón que se está enviando sea autentico y pertenezca al servicio,calendario y fechas correspondientes 
     * a la solicitud que se está realizando
     * @param \Illuminate\Http\Request  $request  ['codigo','id_calendario']
     * @return \Illuminate\Http\Response 
     */
    public function verificar(Request $request)
    {
        try{
       $cupon = Cupon::where('codigo',$request->get('codigo'))->first();

        if($cupon == null) return response()->json(['error' => 'El cupón no existe'],404);  

        if(!$cupon->servicio->calendario->id == $request->get('id_calendario'))  
            return response()->json(['error'=>'El cupón no existe'],404);
        else {
            if(!($cupon->servicio->id == $request->get('servicio_id')))
                return response()->json(['error'=>'El cupón ingresado no es valido para el servicio seleccionado'],404);
            else {

                $hoy = Carbon::now()->format('Y-m-d');
                
                if(($hoy >= $cupon->fecha_inicial && $hoy<= $cupon->fecha_final)) return response()->json(['descuento' => $cupon->porcentaje],200);
                else return response()->json(['error'=> 'El cupon aún no está vigente para su uso'],404);


                
            }
        }
       
        }
        catch(Exception $e){
         return response()->json(null,500);
        }
       

    }


}
