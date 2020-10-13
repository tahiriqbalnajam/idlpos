<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleProducts extends Model
{
    protected $table = 'sale_products';

    protected $fillable = [
        'id', 'sale_id', 'product_id', 'price','quantity','discount'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product')->select('id','name');

    }
}
