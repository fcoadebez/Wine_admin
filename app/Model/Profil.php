<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'profil';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    // public function rType(){
    //   return $this->hasOne("App\Model\WineType", "id", "wine_type_id");
    // }
}