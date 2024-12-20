<?php

namespace App\Repositories;

use App\Models\Size;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class SizeRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = Size::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){
        $size = $this->model::create($data);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Create",
            "type" => "Size",
            "action" => "Create a size ".$data["size"]
        ]);

        return $size;
    }

    public function update(array $data){
        $size = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Size",
            "action" =>
                "Update a size id ".$data["id"]." data ".
                $this->compareDiff("category_id",$size["category_id"],$data["category_id"]).
                $this->compareDiff("size",$size["size"],$data["size"])
        ]);

        $size->update([
            "category_id" => $data["category_id"],
            "size" => $data["size"]
        ]);



        return $size;
    }

    public function delete($id){
        $size = $this->find($id);
        $size->delete();
        return response()->json(
            [
                "message" => "successfully deleted",
                "status" => 200
            ]
        );
    }
}
