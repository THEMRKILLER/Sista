<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dia_habil extends Model
{
   	 protected $table = 'dia_habil';
     protected $fillable = [
        'calendario_id', 'dia',
    ];

         public function horasHabiles()
    	{
       return $this->hasMany('App\hora_habil');
    	}

}
