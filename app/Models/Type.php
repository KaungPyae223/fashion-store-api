<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    /** @use HasFactory<\Database\Factories\TypeFactory> */
    use HasFactory;

    protected $fillable = [
        "type",
        "category_id",
        "gender"
    ];

    public function category() {
        return $this->belongsTo(Category::class,"category_id","id");
    }

    public function product(){
        return $this->hasMany(Product::class,"type_id","id");
    }

}
