<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class QuestionReponse extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'question_reponse';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rUser(){
        return $this->hasOne("App\Model\User", "id", "user_id");
    }
    public function rQuestion(){
        return $this->hasOne("App\Model\Question", "id", "question_id");
    }
    public function rQuestionNext(){
        return $this->hasOne("App\Model\Question", "id", "question_next_id");
    }
}