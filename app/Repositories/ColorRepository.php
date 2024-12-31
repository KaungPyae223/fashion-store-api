<?php

namespace App\Repositories;

use App\Models\Color;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;

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
        $color = $this->model::create($data);

        $this->addAdminActivity([
            "admin_id" => $this->admin_id,
            "method" => "Create",
            "type" => "Color",
            "action" => "Create a color ".$data["color"]
        ]);

        return $color;
    }

    public function update(array $data){
        $color = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $this->admin_id,
            "method" => "Update",
            "type" => "Color",
            "action" => "Update a color ".$color["color"]. " to ".$data["color"]
        ]);

        $color->update([
            "color" => $data["color"],
        ]);

        return $color;
    }

    public function delete($id){
        $size = $this->find($id);
        $size->delete();
       
    }
}
