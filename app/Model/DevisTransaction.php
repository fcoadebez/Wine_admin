<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DevisTransaction extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'devis_transaction';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rDevis(){
        return $this->hasOne("App\Model\Devis", "id", "devis_id");
    }
}
