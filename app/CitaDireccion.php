<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CitaDireccion extends Model
{
    protected $table = 'cita_direccion';
     protected $fillable = [
        'latitud', 'longitud','direccion','referencia'
    ];

       public function cita()
      {
       return $this->belongsTo('App\cita');
      }
}
