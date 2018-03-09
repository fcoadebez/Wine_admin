<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // SETTINGS
	public $timestamps = false;
	protected $table = 'user';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

    // RELATIONS
	protected $hidden = [
		'remember_token',
	];
}