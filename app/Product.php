<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\WishList;
use App\Rating;
use App\Transaction;

class Product extends Model
{
    protected $guarded = [];
    
    protected $table = 'products';

    const AVAILABLE = true;
    const UNAVAILABLE = false;

    public function isAvailable(){
        return $this->available = Product::AVAILABLE;
    }

    /* Relationships */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function wishlists(){
        return $this->belongsToMany(WishList::class);
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function transaction(){
        return $this->hasOne(Transaction::class);
    }
}
