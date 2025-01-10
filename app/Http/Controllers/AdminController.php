<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $adminRepository;

    function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index(Request $request)
    {

        $searchTerm = $request->input('name');
        $searchRole = $request->input('role');

        $admins = Admin::query();

        if ($searchTerm) {
            $admins->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });}

        if ($searchRole && $searchRole != "all") {
            $admins->whereHas('user', function ($q) use ($searchRole) {
                $q->where('role', $searchRole);
            });}



        $admins = $admins->paginate(10);

        $data = AdminResource::collection($admins);


        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $admins->currentPage(),
                'last_page' => $admins->lastPage(),
                'total' => $admins->total(),
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
    public function store(StoreAdminRequest $request)
    {

        $admin = $this->adminRepository->create([
            "name" => $request->name,
            "email" => $request->email,
            "role" => $request->role,
            "password" => Hash::make("Alexa123"),
            "photo" => $request->file("photo"),
            "phone" => $request->phone,
            "address" => $request->address,
        ]);

        return response()->json([
            'message' => 'Color created successfully',
            'data' => new AdminResource($admin)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $admin = $this->adminRepository->update([
            "id" => $request->id,
            "name" => $request->name,
            "role" => $request->role,
            "phone" => $request->phone,
            "address" => $request->address,
            "admin_id" => $request->admin_id,
            "retired" => $request->retired,
        ]);

        return new AdminResource($admin);

    }

    public function updatePhoto (Request $request){

        $request->validate([
            "admin_id" => "required|exists:admins,id",
            "id" => "required|exists:admins,id",
            "photo" => "required|image|mimes:jpeg,png,jpg,gif",
        ]);

        $admin = $this->adminRepository->updatePhoto([
            "id" => $request->id,
            "photo" => $request->photo,
            "admin_id" => $request->admin_id,
        ]);

        return new AdminResource($admin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
