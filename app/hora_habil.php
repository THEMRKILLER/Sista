<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hora_habil extends Model
{
    	 protected $table = 'hora_habil';
    	 protected $fillable = [
        'diahabil_id', 'hora',
    ];
}
