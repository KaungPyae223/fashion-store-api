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

    public function orderDetails(){
        return $this->hasMany(OrderDetails::class,"order_id","id");
    }

    public function customer(){
        return $this->belongsTo(Customer::class,"customer_id","id");
    }

    public function admin(){
        return $this->belongsTo(Admin::class,"admin_id","id");
    }

    public function delivery(){
        return $this->belongsTo(Deliver::class,"delivery_id","id");
    }

    public function payment(){
        return $this->belongsTo(Payment::class,"payment_id","id");
    }

    

}
