<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTypes extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'title','sort_order'];
}
