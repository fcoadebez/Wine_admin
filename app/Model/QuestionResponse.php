<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'question_response';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rQuestion(){
        return $this->hasOne("App\Model\Question", "id", "question_id");
    }

    public function rProfil(){
        return $this->hasOne("App\Model\Profil", "id", "profil_id");
    }
}