<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $table = 'product_stocks';

    protected $fillable = [
        'outlet_id', 'product_id', 'quantity', 'remarks'
    ];

    public function outlet() {
        return $this->belongsTo('App\Outlet');
    }
}