<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Taste extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'taste';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}