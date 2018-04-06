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
    public function rQuestionResponse(){
        return $this->hasMany("App\Model\QuestionResponse", "question_id", "id");
    }
}