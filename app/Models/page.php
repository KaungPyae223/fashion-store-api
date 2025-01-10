<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */

    protected $fillable = ["ads"];

    use HasFactory;

    public function carousel(){
        return $this->hasMany(Hero::class,"page_id","id");
    }

}
