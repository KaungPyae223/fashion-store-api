<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    /** @use HasFactory<\Database\Factories\ProductSizeFactory> */
    use HasFactory;

    protected $fillable = [
        "product_id",
        "size_id",
        "qty",
    ];

    public function size(){
        return $this->belongsTo(Size::class,"size_id","id");
    }
}
