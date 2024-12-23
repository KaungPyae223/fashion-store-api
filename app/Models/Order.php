<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        "customer_id",
        "payment_id",
        "delivery_id",
        "admin_id",
        "total_products",
        "sub_total",
        "tax",
        "total_qty",
        "total_price",
        "name",
        "email",
        "phone",
        "address",
        "note",
        "status",
    ];
}
