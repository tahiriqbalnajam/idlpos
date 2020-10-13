<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function products()
    {
        return $this->hasMany('App\SaleProducts');

    }

    public function customer()
    {
        return $this->belongsTo('App\Accounts')->select(array('id', 'name'));

    }
}
