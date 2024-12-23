<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductSizeRequest;
use App\Http\Requests\UpdateProductSizeRequest;
use App\Repositories\ProductSizeRepository;
use App\Models\ProductSize;

class ProductSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $productSizeRepository;

     function __construct(ProductSizeRepository $productSizeRepository)
     {
         $this->productSizeRepository = $productSizeRepository;
     }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductSizeRequest $request)
    {
        $sizes = $request->size_id;
        $product_id = $request->product_id;

        foreach ($sizes as $size) {
            $this->productSizeRepository->create([
                "product_id" => $product_id,
                "size_id" => $size
            ]);
        }

        return response()->json([
            "message" => "Product sizes added successfully",
            "status" => 200
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSize $productSize)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductSize $productSize)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductSizeRequest $request, $id)
    {
        $productSizes = $this->productSizeRepository->findWhere($id);

        if($productSizes != $request->size_id){
            $productSizes->delete();

            $sizes = $request->size_id;

            foreach ($sizes as $size) {
                $this->productSizeRepository->create([
                    "product_id" => $id,
                    "size_id" => $size
                ]);
            }

            $this->productSizeRepository->addAdminActivity([
                "admin_id" => $request->admin_id,
                "method" => "Update",
                "type" => "Product Size",
                "action" =>
                    "Update a size of product ".$id
            ]);

            return response()->json([
                "message" => "Product sizes updated successfully",
                "status" => 200
            ]);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSize $productSize)
    {
        //
    }
}
