<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','status'];
}
