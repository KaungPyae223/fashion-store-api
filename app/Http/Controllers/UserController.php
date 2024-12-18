<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
    public function store(Request $request)
    {
        $user = $this->userRepository->create([
        'name' => $request->name,
        'email' => $request->email,
        'role' => "Customer",
        'password' => Hash::make($request->password),
        ]);

        return new UserResource($user);

    }

    public function updatePassword(Request $request)
    {

        $validatedData = $request->validate([
            "id" => "required|integer|exists:users,id",
            "password" => "required|string|min:8",
            "old_password" => "required"
        ]);

        try {


            $user = $this->userRepository->find($validatedData["id"]);

            if(Hash::check($validatedData["old_password"],$user->password)){
                $user -> password = Hash::make($validatedData["password"]);
                $user -> update();

                return response()->json([
                    "message" => "Password changed successfully.",
                    "data" => $user,
                ], 200);


            }

            return response()->json([
                "error" => "Your Password is wrong",
            ], 401);

        } catch (\Exception $e) {

            return response()->json([
                "message" => "Failed to change password.",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function updateName (Request $request){

        $validatedData = $request->validate([
            "id" => "required|integer|exists:users,id",
            "name" => "required|string",
        ]);

        try {

            $user = $this->userRepository->updateName([
                "id" => $validatedData["id"],
                "name" => $validatedData["name"]
            ]);

            return response()->json([
                "message" => "Name changed successfully.",
                "data" => $user,
            ], 200);



        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to change password.",
                "error" => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
