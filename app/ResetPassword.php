<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
	protected $table = 'password_resets';
	protected $fillable = [
        'email', 'token', 'created_at',
    ];
    public $timestamps = [ "created_at" ]; // enable only to created_at

}
