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

    // RELATIONS
    public function rDevis(){
        return $this->hasMany("App\Model\Devis", "client_id", "id");
    }
}
