<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo',
        'phone',
        'address',
        'retired',
    ];

    public function user(){
        return $this->hasOne(User::class,"id","user_id");
    }

}
