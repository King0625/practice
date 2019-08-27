<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\User;

class Transaction extends Model
{
    public $guarded = [];

    /* Relationships */
    public function product(){
        return $this->hasOne(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
