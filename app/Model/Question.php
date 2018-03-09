<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'question';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rUser(){
        return $this->hasOne("App\Model\User", "id", "user_id");
    }
    public function rQuestionReponses(){
        return $this->hasMany("App\Model\QuestionReponse", "question_id", "id");
    }
}