<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
	protected $table = 'cupon';
    protected $fillable = [
        'id','servicio_id','fecha_inicial','fecha_final','porcentaje' 
    ];

    public function servicios()
    {
        return $this->belongsTo('App\tipo');
    }

    
}
