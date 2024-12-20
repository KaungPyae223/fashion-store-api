<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $brandRepository;

     function __construct(BrandRepository $brandRepository)
     {
        $this->brandRepository = $brandRepository;
     }

    public function index()
    {
        $query = Brand::all();

        return BrandResource::collection($query);

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
    public function store(StoreBrandRequest $request)
    {
        $brand = $this->brandRepository->create([
            "name" => $request->name,
            "photo" => $request->file("photo"),
            "admin_id" => $request->admin_id,
        ]);

        return new BrandResource($brand);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    public function updatePhoto (Request $request){

        $request->validate([
            "admin_id" => "required|exists:admins,id",
            "id" => "required|exists:brands,id",
            "photo" => "required|image|mimes:jpeg,png,jpg,gif",
        ]);

        $admin = $this->brandRepository->updateImage([
            "id" => $request->id,
            "photo" => $request->file("photo"),
            "admin_id" => $request->admin_id,
        ]);

        return new BrandResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {

        $brand = $this->brandRepository->update([
            "name" => $request->name,
            "id" => $brand->id,
            "admin_id" => $request->admin_id,
        ]);

        return new BrandResource($brand);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        return $this->brandRepository->delete($brand->id);
    }
}
