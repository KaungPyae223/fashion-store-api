<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliverRequest;
use App\Http\Requests\UpdateDeliverRequest;
use App\Http\Resources\DeliverResource;
use App\Models\Deliver;
use App\Repositories\DeliverRepository;
use Illuminate\Http\Request;

class DeliverController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $deliverRepository;

     function __construct(DeliverRepository $deliverRepository)
     {
            $this->deliverRepository = $deliverRepository;
     }

    public function index(Request $request)
    {

        $searchTerm = $request->input('q');


        $query = Deliver::query();

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $deliveries = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = DeliverResource::collection($deliveries);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $deliveries->currentPage(),
                'last_page' => $deliveries->lastPage(),
                'total' => $deliveries->total(),
            ],
        ]);

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
    public function store(StoreDeliverRequest $request)
    {
        $deliver = $this->deliverRepository->create($request->validated());

        return response()->json([
            'message' => 'Delivery created successfully',
            'data' => new DeliverResource($deliver)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Deliver $deliver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deliver $deliver)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliverRequest $request, $id)
    {


        $deliver = $this->deliverRepository->update(
            array_merge($request->validated(),["id" => $id])
        );

        return new DeliverResource($deliver);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deliver $deliver)
    {
        $deliver->order->count();

        if($deliver == 0){
            $this->deliverRepository->delete($deliver->id);
            return response()->json(['message' => 'Delivery deleted successfully']);
        }else{
            return response()->json([
                "status" => 409,
                "error" => "Conflict",
                "message" => "Resource cannot be deleted due to existing dependencies."
            ],409);
        }
    }
}
