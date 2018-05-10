<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientProfil extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'client_profil';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
