<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminMonitoringRequest;
use App\Http\Requests\UpdateAdminMonitoringRequest;
use App\Http\Resources\AdminMonitoringResource;
use App\Models\AdminMonitoring;
use Illuminate\Http\Request;

class AdminMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $searchTerm = $request->input('name');
        $searchRole = $request->input('role');
        $time = $request->input("time");


        $query = AdminMonitoring::query();

        if ($searchTerm) {
            $query->whereHas('admin.user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });}

        if ($searchRole && $searchRole != "all") {
            $query->whereHas('admin.user', function ($q) use ($searchRole) {
                $q->where('role', $searchRole);
            });}

        if (preg_match('/^\d{4}-\d{2}$/', $time)) {
            $query->whereMonth('created_at', '=', date('m', strtotime($time)))
                    ->whereYear('created_at', '=', date('Y', strtotime($time)));
        }

        $query = $query->paginate(10);

        $activities = AdminMonitoringResource::collection($query);

        return response()->json([
            "data" => $activities,
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
