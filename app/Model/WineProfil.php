<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WineProfil extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'wine_profil';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    // public function rType(){
    //   return $this->hasOne("App\Model\WineType", "id", "wine_type_id");
    // }
}