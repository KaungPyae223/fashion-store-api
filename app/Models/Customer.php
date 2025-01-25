<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'city',
        'township',
        'zip_code',
        'address'
    ];

    public function user(){
        return $this->hasOne(User::class,"id","user_id");
    }

    public function orders(){
        return $this->hasMany(Order::class,"customer_id","id");
    }

    public function Question(){
        return $this->hasMany(CustomerQuestion::class,"customer_id","id");
    }

    public function wishlist() {
        return $this->hasMany(Wishlist::class,"customer_id","id");
    }

}
