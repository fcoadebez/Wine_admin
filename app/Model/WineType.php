<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WineType extends Model
{
    // SETTINGS
    public $timestamps = false;
    protected $table = 'wine_type';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}