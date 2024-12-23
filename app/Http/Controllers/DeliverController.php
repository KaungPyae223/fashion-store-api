<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliverRequest;
use App\Http\Requests\UpdateDeliverRequest;
use App\Http\Resources\DeliverResource;
use App\Models\Deliver;
use App\Repositories\DeliverRepository;

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
    public function store(StoreDeliverRequest $request)
    {
        $deliver = $this->deliverRepository->create($request->validated());

        return new DeliverResource($deliver);

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
        //
    }
}
