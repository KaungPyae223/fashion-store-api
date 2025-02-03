<?php

namespace App\Repositories;

use App\Models\Hero;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HeroRepository extends BasicFunctions
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Hero::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    function createHero(array $data) {


        $url = $this->storePhoto($data["image"],"heroImage");

        try{

            DB::beginTransaction();

            $hero = $this->model::create([
                'title' => $data["title"],
                'subtitle' => $data["subtitle"],
                'image' => $url,
                'link' => $data["link"],
                'link_title' => $data["link_title"],
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Carousel",
                "action" => "Create a carousel".$hero->id
            ]);

            DB::commit();

            return $hero;

        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
