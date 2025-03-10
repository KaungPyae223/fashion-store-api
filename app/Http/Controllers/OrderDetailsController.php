<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderDetailsRequest;
use App\Http\Requests\UpdateOrderDetailsRequest;
use App\Models\OrderDetails;

class OrderDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    public function store(StoreOrderDetailsRequest $request)
    {
        $order = OrderDetails::create($request->validated());

        return response()->json($order, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(OrderDetails $orderDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderDetails $orderDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderDetailsRequest $request, OrderDetails $orderDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderDetails $orderDetails)
    {
        //
    }
}
