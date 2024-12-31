<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    /** @use HasFactory<\Database\Factories\SizeFactory> */
    use HasFactory;

    protected $fillable = [
        'size',
        'category_id',
    ];

    public function category() {
        return $this->belongsTo(Category::class,"category_id","id");
    }

    public function product() {
        return $this->belongsToMany(Product::class,"product_sizes","size_id","product_id","id","id");
    }
}
