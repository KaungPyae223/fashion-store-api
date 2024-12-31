<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeRequest;
use App\Http\Requests\UpdateTypeRequest;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use App\Repositories\TypeRepository;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $typeRepository;

    function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');
        $category = $request->input('category');


        $query = Type::query();

        if ($searchTerm) {
            $query->where('type', 'like', '%' . $searchTerm . '%');
        }

        if ($category && $category != "all") {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category', $category); // Adjust 'name' to the correct column in the categories table
            });
        }

        // Paginate the results
        $types = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = TypeResource::collection($types);

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
    public function store(StoreTypeRequest $request)
    {

        $type = $this->typeRepository->create([
            "category_id" => $request->category_id,
            "type" => $request->type,
        ]);

        return response()->json([
            'message' => 'Color created successfully',
            'data' => new TypeResource($type)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        return new TypeResource($type);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Type $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeRequest $request, Type $type)
    {
        $type = $this->typeRepository->update([
            "id" => $type->id,
            "category_id" => $request->category_id,
            "type" => $request->type,
        ]);

        return new TypeResource($type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        $count = $type->product->count();


        if($count == 0){
            $this->typeRepository->delete($type->id);
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
