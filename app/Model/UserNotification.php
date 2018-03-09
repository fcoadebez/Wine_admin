<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'user_notification';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    // RELATIONS
    public function rUser(){
        return $this->hasOne("App\Model\User", "id", "user_id");
    }
}