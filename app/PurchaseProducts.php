<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseProducts extends Model
{
    protected $table = 'purchase_products';

    protected $fillable = [
        'id', 'purchase_id', 'product_id', 'price','quantity','discount'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product')->select('id','name');

    }
}
