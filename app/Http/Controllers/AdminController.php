<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Models\User;
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



    }

    public function AdminData(){

        $admin = $this->adminRepository->getAdmin();

        return response()->json($admin);

    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 401);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $user->tokens()->delete();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function restartPassword($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update(['password' => Hash::make('admin')]);
        $user->tokens()->delete();

        return response()->json(['message' => 'Password restarted successfully']);
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
            "password" => Hash::make("admin"),
            "photo" => $request->file("photo"),
            "phone" => $request->phone,
            "address" => $request->address,
        ]);

        return response()->json([
            'message' => 'Admin created successfully',
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
    public function update(UpdateAdminRequest $request,$id)
    {
        $admin = $this->adminRepository->update([
            "id" => $id,
            "name" => $request->name,
            "role" => $request->role,
            "email" => $request->email,
            "phone" => $request->phone,
            "address" => $request->address,
        ]);

        return new AdminResource($admin);

    }

    public function updatePhoto (Request $request){

        $request->validate([
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
