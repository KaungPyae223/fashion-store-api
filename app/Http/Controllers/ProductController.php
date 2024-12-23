<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $productRepository;

    public function __construct(ProductRepository $productRepository){
        $this->productRepository = $productRepository;
    }

    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->create([
            "admin_id" => $request->admin_id,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "color_id" => $request->color_id,
            "name" => $request->name,
            "cover_photo" => $request->file("cover_photo"),
            "price" => $request->price,
            "description" => $request->description,
            "status" => $request->status,
            "gender" => $request->gender,
            // "product_size" => $request->size_id,
        ]);


        return new ProductResource($product);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    public function updatePhoto(Request $request)
    {

        $request->validate([
            "id" => "required|exists:products,id",
            "cover_photo" => "required|image|mimes:jpeg,png,jpg,gif",
            "admin_id" => "required|exists:admins,id"
        ]);

        $product = $this->productRepository->updatePhoto([
            "id" => $request->id,
            "cover_photo" => $request->file("cover_photo"),
            "admin_id" => $request->admin_id
        ]);

        return new ProductResource($product);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {

        $product = $this->productRepository->update([
            "id" => $product->id,
            "type_id" => $request->type_id,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "color_id" => $request->color_id,
            "name" => $request->name,
            "price" => $request->price,
            "description" => $request->description,
            "status" => $request->status,
            "gender" => $request->gender,
            "admin_id" => $request->admin_id,
            // "product_size" => $request->size_id,

        ]);


        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

    }

    public function deleteProduct(Request $request)
    {

        $product = $this->productRepository->find($request->id);

        $product->update([
            "is_delete" => true
        ]);

        $this->productRepository->addAdminActivity([
            "admin_id" => $request->admin_id,
            "method" => "Delete",
            "type" => "Product",
            "action" => "Delete a product ".$product->name
        ]);

        return response()->json(["message" => "Product deleted successfully"]);
    }

    public function restoreProduct(Request $request)
    {

        $product = $this->productRepository->find($request->id);

        $product->update([
            "is_delete" => false
        ]);

        $this->productRepository->addAdminActivity([
            "admin_id" => $request->admin_id,
            "method" => "Delete",
            "type" => "Product",
            "action" => "Restore a product ".$product->name
        ]);

        return response()->json(["message" => "Product restore successfully"]);
    }

}
