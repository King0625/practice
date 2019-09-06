<?php

namespace App;

use App\Product;
use App\User;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    protected $table = 'wishlists';

    protected $guarded = [];

    public function product(){
        return $this->hasOne(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
