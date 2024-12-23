<?php

namespace App\Repositories;

use App\Models\ProductPhoto;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class ProductImageRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = ProductPhoto::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function findWhere($id){
        return $this->model::where("product_id",$id)->get();
    }

    public function create(array $data){

        $imageURL = $this->storePhoto($data["photo"],"productImage");

        $this->model::create([
            "product_id" => $data["product_id"],
            "photo" => $imageURL
        ]);

    }

    public function update(array $data){

    }

    public function delete($id){
        $productPhoto = $this->find($id);

        $this->deletePhoto($productPhoto->Photo);

        return response()->json($productPhoto);

        $productPhoto->delete();
    }
}
