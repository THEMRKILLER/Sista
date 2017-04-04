<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class informacion_extra extends Model
{
	 protected $table = 'informacion_extra';
     protected $fillable = [
        'id','user_id','dominio'
        ];
         public function user()
     {
        return $this->belongsTo('App\User');
     }
}
