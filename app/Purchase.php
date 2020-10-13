<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public function products()
    {
        return $this->hasMany('App\PurchaseProducts')->select('id','purchase_id','product_id','quantity','price','discount');

    }
    public function supplier()
    {
        return $this->belongsTo('App\Accounts')->select(array('id', 'name'));

    }
}
