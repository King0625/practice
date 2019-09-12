<?php

namespace App;

use App\Rating;
use App\Profile;
use App\Product;
use App\WishList;
use App\Transaction;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; 

    const ADMIN_USER = true;
    const REGULAR_USER = false;

    // public function getRouteKeyName()
    // {
    //     return 'name';
    // }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'superuser', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuperUser(){
        return $this->superuser == User::ADMIN_USER;
    }

    /* Relationships */

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function wishlist(){
        return $this->hasOne(WishList::class);
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }
}
