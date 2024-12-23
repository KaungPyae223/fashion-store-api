<?php

namespace App\Repositories;

use App\Models\ProductSize;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class ProductSizeRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = ProductSize::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){
        $product_size = $this->model::create($data);

        return $product_size;
    }

    public function findWhere($id){
        return $this->model::where("product_id",$id)->get();
    }

    public function update(array $data){

    }

    public function delete($id){
        $product_size = $this->find($id);
        $product_size->delete();
    }
}
