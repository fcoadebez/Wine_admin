<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientResponse extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'client_response';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}