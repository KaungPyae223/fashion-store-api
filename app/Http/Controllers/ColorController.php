<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Repositories\ColorRepository;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $colorRepository;

    function __construct(ColorRepository $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');


        $query = Color::query();

        if ($searchTerm) {
            $query->where('color', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $colors = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = ColorResource::collection($colors);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $colors->currentPage(),
                'last_page' => $colors->lastPage(),
                'total' => $colors->total(),
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
    public function store(StoreColorRequest $request)
    {
        $color = $this->colorRepository->create(
            [
                "color" => $request->color,
            ]
        );

        return response()->json([
            'message' => 'Color created successfully',
            'data' => new ColorResource($color)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        return new ColorResource($color);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Color $color)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateColorRequest $request, Color $color)
    {
        $color = $this->colorRepository->update(
            [
                "id" => $color->id,
                "color" => $request->color,
            ]
        );

        return new ColorResource($color);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {

        $count = $color->product->count();


        if($count == 0){
            $this->colorRepository->delete($color->id);
            return response()->json(['message' => 'Color deleted successfully']);
        }else{
            return response()->json([
                "status" => 409,
                "error" => "Conflict",
                "message" => "Resource cannot be deleted due to existing dependencies."
            ],409);
        }


    }
}
