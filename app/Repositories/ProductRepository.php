<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\ProductSize;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BasicFunctions implements BaseRepository{

    protected $model;
    protected $productSizeModel;
    protected $productPhotoModel;
    protected $admin_id;

    function __construct()
    {
        $this->model = Product::class;
        $this->productSizeModel = ProductSize::class;
        $this->admin_id = Auth::user()->admin->id;
        $this->productPhotoModel = ProductPhoto::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){


        $sizeIDs = explode(',', $data["size_id"]);
        $detailsPhotos = $data["details_photos"];

        try {

            DB::beginTransaction();

            $product = $this->model::create([
                "type_id" => $data["type_id"],
                "brand_id" => $data["brand_id"],
                "category_id" => $data["category_id"],
                "color_id" => $data["color_id"],
                "name" => $data["name"],
                "cover_photo" => $this->storePhoto($data["cover_photo"],"productCoverImage"),
                "price" => $data["price"],
                "description" => $data["description"],
                "status" => $data["status"],
                "gender" => $data["gender"],
            ]);

            foreach($detailsPhotos as $photo){
                $photoURL = $this->storePhoto($photo,"productDetailsPhoto");

                $this->productPhotoModel::create([
                    "product_id" => $product->id,
                    "photo" => $photoURL
                ]);
            }

            foreach($sizeIDs as $size){
                $this->productSizeModel::create([
                    "product_id" => $product->id,
                    "size_id" => $size
                ]);
            }

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Product",
                "action" => "Create a new product ".$data["name"]
            ]);

            DB::commit();

            return $product;

        }
        catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return $e;
        }

    }

    public function update(array $data){
        $product = $this->model::find($data["id"]);

        $product_sizes = $this->productSizeModel::where("product_id",$product->id);

        // if($product_sizes != $data["size_id"]){
        //     $product_sizes->delete();

        //     foreach($data["size_id"] as $size){
        //         $this->productSizeModel::create([
        //             "product_id" => $product->id,
        //             "size_id" => $size
        //         ]);
        //     }
        // }



        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Product",
            "action" =>
            "Update a product ". $data["id"].
            $this->compareDiff("type_id",$product->type_id,$data["type_id"]).
            $this->compareDiff("brand_id",$product->brand_id,$data["brand_id"]).
            $this->compareDiff("category_id",$product->category_id,$data["category_id"]).
            $this->compareDiff("color_id",$product->color_id,$data["color_id"]).
            $this->compareDiff("name",$product->name,$data["name"]).
            $this->compareDiff("price",$product->price,$data["price"]).
            $this->compareDiff("description",$product->description,$data["description"]).
            $this->compareDiff("status",$product->status,$data["status"]),
            $this->compareDiff("gender",$product->status,$data["gender"]),
            // $this->compareDiff("size_id",$product_sizes,$data["size_id"]),
        ]);

        $product->update([
            "type_id" => $data["type_id"],
            "brand_id" => $data["brand_id"],
            "category_id" => $data["category_id"],
            "color_id" => $data["color_id"],
            "name" => $data["name"],
            "price" => $data["price"],
            "description" => $data["description"],
            "status" => $data["status"],
            "gender" => $data["gender"],

        ]);



        return $product;
    }

    public function updatePhoto(array $data){

        $brand = $this->find($data["id"]);

        $this->deletePhoto($brand->cover_photo);

        $imageURL = $this->storePhoto($data["cover_photo"],"productCoverImage");

        $brand->update([
            "cover_photo" => $imageURL
        ]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Product",
            "action" => "Update a Product Cover ". $brand->name . " photo"
        ]);

        return $brand;

    }

    public function delete($id){


    }



}
