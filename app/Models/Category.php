<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public function product() {
        return $this->hasMany(Product::class,"category_id","id");
    }

    public function type() {
        return $this->hasMany(Type::class,"category_id","id");
    }

    public function size() {
        return $this->hasMany(Size::class,"category_id","id");
    }

}
