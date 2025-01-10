<?php

namespace App\Repositories;

use App\Models\Size;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SizeRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Size::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        try{

            DB::beginTransaction();

            $size = $this->model::create($data);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Size",
                "action" => "Create a size ".$data["size"]
            ]);

            DB::commit();

            return $size;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }


    }

    public function update(array $data){
        $size = $this->find($data["id"]);


        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
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

            DB::commit();

            return $size;

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
