<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'address','phone','account_type_id','status'];

    public function account_type()
    {
        return $this->belongsTo('App\AccountTypes');
    }

}
