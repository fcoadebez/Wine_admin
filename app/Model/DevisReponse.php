<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DevisReponse extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'devis_reponse';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rDevis(){
        return $this->hasOne("App\Model\Devis", "id", "devis_id");
    }
    public function rQuestionReponse(){
        return $this->hasOne("App\Model\QuestionReponse", "id", "response_id");
    }
}
