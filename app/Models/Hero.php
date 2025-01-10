<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'link_title',
    ];

    /** @use HasFactory<\Database\Factories\HeroFactory> */
    use HasFactory;
}
