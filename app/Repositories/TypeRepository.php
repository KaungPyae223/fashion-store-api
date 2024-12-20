<?php

namespace App\Repositories;

use App\Models\Type;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class TypeRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = Type::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){
        $type = $this->model::create($data);
        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Create",
            "type" => "Type",
            "action" => "Create a type ".$data["type"]
        ]);
        return $type;
    }

    public function update(array $data){
        $type = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Type",
            "action" =>
                "Update a type ".$data["id"]." data ".
                $this->compareDiff("category_id",$type["category_id"],$data["category_id"]).
                $this->compareDiff("type",$type["type"],$data["type"])
        ]);

        $type->update([
            "category_id" => $data["category_id"],
            "type" => $data["type"]
        ]);

        return $type;
    }

    public function delete($id){
        $type = $this->find($id);
        $type->delete();
        return response()->json(
            [
                "message" => "successfully deleted",
                "status" => 200
            ]
        );
    }
}
