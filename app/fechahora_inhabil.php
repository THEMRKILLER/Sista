<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class fechahora_inhabil extends Model
{
   	 protected $table = 'fechahora_inhabil';
     protected $fillable = [
        'fechainhabil_id', 'hora',
    ];
}
