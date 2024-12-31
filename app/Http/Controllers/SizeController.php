<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use App\Repositories\SizeRepository;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $sizeRepository;

    function __construct(SizeRepository $sizeRepository)
    {
        $this->sizeRepository = $sizeRepository;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $category = $request->input('category');

        // Build the query
        $query = Size::query();

        if ($searchTerm) {
            $query->where('size', 'like', '%' . $searchTerm . '%');
        }

        if ($category && $category != "all") {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category', $category); // Adjust 'name' to the correct column in the categories table
            });
        }

        // Paginate the results
        $sizes = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = SizeResource::collection($sizes);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $sizes->currentPage(),
                'last_page' => $sizes->lastPage(),
                'total' => $sizes->total(),
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
    public function store(StoreSizeRequest $request)
    {

        $size = $this->sizeRepository->create([
            "category_id" => $request->category_id,
            "size" => $request->size,
        ]);

        return response()->json([
            'message' => 'Size created successfully',
            'data' => new SizeResource($size)
        ], 201);



    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Size $size)
    {
        return new SizeResource($size);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeRequest $request, Size $size)
    {
        $size = $this->sizeRepository->update([
            "id" => $size->id,
            "category_id" => $request->category_id,
            "size" => $request->size,
        ]);

        return new SizeResource($size);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {

        $count = $size->product->count();

        if($count == 0){
            $this->sizeRepository->delete($size->id);
            return response()->json(['message' => 'Size deleted successfully']);
        }else{
            return response()->json([
                "status" => 409,
                "error" => "Conflict",
                "message" => "Resource cannot be deleted due to existing dependencies."
            ],409);
        }


    }
}
