<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminMonitoringRequest;
use App\Http\Requests\UpdateAdminMonitoringRequest;
use App\Http\Resources\AdminMonitoringResource;
use App\Models\AdminMonitoring;

class AdminMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = AdminMonitoring::paginate(10);

        $brands = AdminMonitoringResource::collection($query);

        return response()->json([
            "data" => $brands,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
            "status" => 200,
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
    public function store(StoreAdminMonitoringRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminMonitoring $adminMonitoring)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminMonitoring $adminMonitoring)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminMonitoringRequest $request, AdminMonitoring $adminMonitoring)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminMonitoring $adminMonitoring)
    {
        //
    }
}
