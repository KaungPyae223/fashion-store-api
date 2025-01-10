<?php

namespace App\Repositories;

use App\Models\Type;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypeRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Type::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        try{

            DB::beginTransaction();

            $type = $this->model::create($data);


            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Type",
                "action" => "Create a type ".$data["type"]
            ]);

            DB::commit();

            return $type;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }


    }

    public function update(array $data){
        $type = $this->find($data["id"]);

        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
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

            DB::commit();

            return $type;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }


    }

    public function delete($id){
        $type = $this->find($id);
        $type->delete();

    }
}
