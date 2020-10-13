<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'code', 'category_id', 'purchase_price', 'sale_price','wholesale_price', 'status'
    ];

    public function category() {
        return $this->belongsTo('App\ProductCategory', 'category_id');
    }
    public function stock() {
        return $this->hasMany('App\ProductStock');
    }
}
