<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
