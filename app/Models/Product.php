<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        "type_id",
        "brand_id",
        "category_id",
        "color_id",
        "name",
        "cover_photo",
        "price",
        "description",
        "status",
        "gender",
        "is_delete",
    ];

    public function type(){
        return $this->belongsTo(Type::class,"type_id","id");
    }
    public function brand(){
        return $this->belongsTo(Brand::class,"brand_id","id");
    }
    public function category(){
        return $this->belongsTo(Category::class,"category_id","id");
    }
    public function size(){
        return $this->belongsToMany(Size::class,"product_sizes","product_id","size_id","id","id");
    }
    public function color(){
        return $this->belongsTo(Color::class,"color_id","id");
    }
    public function productPhoto() {
        return $this->hasMany(ProductPhoto::class);
    }

    public function review(){
        return $this->hasMany(Review::class,"product_id","id");
    }

}
