<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'devis';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rClient(){
        return $this->hasOne("App\Model\Client", "id", "client_id");
    }
    public function rStatus(){
        return $this->hasOne("App\Model\DevisStatus", "id", "status_id");
    }
    public function rDevisTransactions(){
        return $this->hasMany("App\Model\DevisTransaction", "devis_id", "id");
    }
}
