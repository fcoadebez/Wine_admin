<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'config';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public static function getMessage($name){
        $t = self::where("name", "=", $name);

        if($t->count()<=0)
            return "";

        return $t->first()->value;
    }
}
