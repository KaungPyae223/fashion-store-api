<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliver extends Model
{
    /** @use HasFactory<\Database\Factories\DeliverFactory> */
    use HasFactory;

    protected $fillable = [
        "name",
        "email",
        "phone",
        "address",
        "status"
    ];

    public function order() {
        return $this->hasMany(Order::class,"delivery_id","id");
    }

}
