<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory;

    protected $fillable = [
        "admin_id",
        "title",
        "photo",
        "content"
    ];

    public function admin(){
        return $this->belongsTo(Admin::class,"admin_id","id");
    }

}
