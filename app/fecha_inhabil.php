<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fecha_inhabil extends Model
{
   	 protected $table = 'fecha_inhabil';
     protected $fillable = [
        'calendario_id', 'fecha', 'completo',
    ];

     public function horasInhabiles()
    	{
        return $this->hasMany('App\fechahora_inhabil');
    	}    
}
