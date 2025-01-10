<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandRepository extends BasicFunctions implements BaseRepository {

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Brand::class;
        $this->admin_id = Auth::user()->admin->id;

    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        $imageURL = $this->storePhoto($data["photo"],"brandImage");


        try{

            DB::beginTransaction();

            $brand = $this->model::create([
                "name" => $data["name"],
                "photo" => $imageURL
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Brand",
                "action" => "Create a brand ".$data["name"]
            ]);

            DB::commit();

            return $brand;


        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

    public function update(array $data){

        $brand = $this->find($data["id"]);

        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Brand",
                "action" => "Update a brand ".$brand->name. " to ".$data["name"]
            ]);

            $brand->update([
                "name" => $data["name"]
            ]);

            DB::commit();

            return $brand;


        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }

    }



    public function updateImage(array $data){

        $brand = $this->find($data["id"]);

        try{

            DB::beginTransaction();

            $this->deletePhoto($brand->photo);

            $imageURL = $this->storePhoto($data["photo"],"adminImage");

            $brand->update([
                "photo" => $imageURL
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Brand",
                "action" => "Update a brand ". $brand->name . " photo"
            ]);

            DB::commit();

            return $brand;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

    public function delete($id){

        $brand = $this->find($id);

        $this->deletePhoto($brand->photo);

        $brand->delete();



    }
}
