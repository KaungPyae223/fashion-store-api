<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\page;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getHeaderAds(){

        $ads = page::find(1)->ads;

        return response()->json([
            "ads" => $ads
        ],200);

    }

    public function getCarousels(){

        $hero = Hero::all()->map(function($carousel){
            return [
                "id" => $carousel->id,
                "image" => $carousel->image,
                "title" => $carousel->title,
                "sub_title" => $carousel->subtitle,
                "link" => $carousel->link,
                "link_title" => $carousel->link_title
            ];
        });

        return response()->json($hero);

    }

}
