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
                "profit_percent" => $data['profit_percent'],
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

            $product->size()->attach($sizeIDs);

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

        $product_sizes = $product->size->pluck('id')->toArray();

        $original_sizes = implode(',', $product_sizes);

        $sizeIDs = explode(',', $data["size_id"]);

        $sizesToDelete = array_diff($product_sizes, $sizeIDs);

        $sizesToAdd = array_diff($sizeIDs, $product_sizes);


        try {

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
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
                !empty($sizesToDelete) || !empty($sizesToAdd) ? $this->compareDiff("size_id",$original_sizes,$data["size_id"]):null,
            ]);


            if (!empty($sizesToDelete)) {
                $product->size()->detach($sizesToDelete); // Detach sizes that are no longer needed
            }


            if (!empty($sizesToAdd)) {
                $product->size()->attach($sizesToAdd); // Attach new sizes
            }



            $product->update([
                "type_id" => $data["type_id"],
                "brand_id" => $data["brand_id"],
                "category_id" => $data["category_id"],
                "color_id" => $data["color_id"],
                "name" => $data["name"],
                "price" => $data["price"],
                "profit_percent" => $data["profit_percent"],
                "description" => $data["description"],
                "status" => $data["status"],
                "gender" => $data["gender"],

            ]);

            DB::commit();

            return $product;

        }
        catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }


    }

    public function updateDetailsPhoto(array $data){


        $product = $this->find($data["id"]);

        $product->productPhoto->map(function($photo){
            $this->deletePhoto($photo->photo);
            $photo->delete();
        });


        $newPhoto = $data["details_photos"];

        foreach($newPhoto as $photo){
            $photoURL = $this->storePhoto($photo,"productDetailsPhoto");

            $this->productPhotoModel::create([
                "product_id" => $product->id,
                "photo" => $photoURL
            ]);
        }

        return $product;

    }

    public function updateCoverPhoto(array $data){

        $product = $this->find($data["id"]);

        $this->deletePhoto($product->cover_photo);

        $imageURL = $this->storePhoto($data["cover_photo"],"productCoverImage");

        try{

            DB::beginTransaction();

            $product->update([
                "cover_photo" => $imageURL
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Product",
                "action" => "Update a Product Cover ". $product->name . " photo"
            ]);

            DB::commit();

            return $product;

        }catch (\Exception $e) {

            DB::rollBack();
            return $e;
        }



    }



    public function delete($id){

        $product = $this->find($id);


        try{

            DB::beginTransaction();

            $product->update([
                "is_delete" => true
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Delete",
                "type" => "Product",
                "action" => "Delete a product ".$product->name
            ]);

            DB::commit();


        }catch (\Exception $e) {

            DB::rollBack();
            return $e;
        }

    }

    public function restoreProduct($id){

        $product = $this->find($id);


        try{

            DB::beginTransaction();

            $product->update([
                "is_delete" => false
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Delete",
                "type" => "Product",
                "action" => "Restore a product ".$product->name
            ]);

            DB::commit();

        }catch (\Exception $e) {

            DB::rollBack();
            return $e;
        }


    }

}
