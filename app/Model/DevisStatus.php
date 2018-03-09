<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DevisStatus extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'devis_status';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rUser(){
        return $this->hasOne("App\Model\User", "id", "user_id");
    }
    public function rDevis(){
        return $this->hasMany("App\Model\Devis", "status_id", "id");
    }
}