<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Realisation extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'realisation';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rUser(){
        return $this->hasOne("App\Model\User", "id", "user_id");
    }
}