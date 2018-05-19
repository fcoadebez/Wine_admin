<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WineDrink extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'wine_drink';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rWines(){
        return $this->hasMany("App\Model\Wine", "id", "wine_id");
    }
}