<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\User;

class Transaction extends Model
{
    protected $table = 'transactions';
    public $guarded = [];

    const TRADED = true;
    const UNTRADED = false;

    /* Relationships */
    public function product(){
        return $this->hasOne(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function isTraded(){
        return $this->traded = Transaction::TRADED;
    }
}
