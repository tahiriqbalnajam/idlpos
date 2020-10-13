<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountTransactions extends Model
{
    public function account()
    {
        return $this->belongsTo('App\Accounts')->select(array('id', 'name'));

    }
}
