<?php

namespace App\Repositories;

use App\Models\Color;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColorRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Color::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        try{

            DB::beginTransaction();

            $color = $this->model::create($data);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Color",
                "action" => "Create a color ".$data["color"]
            ]);

            DB::commit();

            return $color;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }


    }

    public function update(array $data){
        $color = $this->find($data["id"]);

        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Color",
                "action" => "Update a color ".$color["color"]. " to ".$data["color"]
            ]);

            $color->update([
                "color" => $data["color"],
            ]);

            DB::commit();

            return $color;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

    public function delete($id){
        $size = $this->find($id);
        $size->delete();

    }
}
