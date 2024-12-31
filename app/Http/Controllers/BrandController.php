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

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');


        $query = Brand::query();

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $types = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = BrandResource::collection($types);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $types->currentPage(),
                'last_page' => $types->lastPage(),
                'total' => $types->total(),
            ],
        ]);

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
        ]);

        return response()->json([
            'message' => 'Brand created successfully',
            'data' => new BrandResource($brand)
        ], 201);
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
            "id" => "required|exists:brands,id",
            "photo" => "required|image|mimes:jpeg,png,jpg,gif",
        ]);

        $admin = $this->brandRepository->updateImage([
            "id" => $request->id,
            "photo" => $request->file("photo"),
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
        ]);

        return new BrandResource($brand);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $count = $brand->product->count();


        if($count == 0){
            $this->brandRepository->delete($brand->id);
            return response()->json(['message' => 'Type deleted successfully']);
        }else{
            return response()->json([
                "status" => 409,
                "error" => "Conflict",
                "message" => "Resource cannot be deleted due to existing dependencies."
            ],409);
        }
    }
}
