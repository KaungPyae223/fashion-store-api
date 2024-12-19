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

}
