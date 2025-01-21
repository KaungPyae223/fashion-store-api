<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contract\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerRepository implements BaseRepository
{

    protected $customerModel;
    protected $userModel;

    function __construct()
    {
        $this->customerModel = Customer::class;
        $this->userModel = User::class;
    }

    public function find($id){
        $customer = $this->customerModel::find($id);
        return $customer;
    }

    public function findUser($id){
        $user = $this->userModel::find($id);
        return $user;
    }

    public function create(array $data){
        $user = $this->userModel::create($data);
        $customer = $this->customerModel::create(["user_id" => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;


        return [
            ['token' => $token, 'customer' => $customer]
        ];
    }

    public function update(array $data){

        try{

            DB::beginTransaction();


            $user = Auth::user();

            if (!Hash::check($data["password"], $user->password)) {
                return response()->json(['message' => 'Password is incorrect'], 401);
            }

            $user->update(["name" => $data["name"]]);

            $customer = $user->customer;
            $customer->update([
                "phone" => $data["phone"],
                "address" => $data["address"],
            ]);

            DB::commit();

            return $customer;

        }catch(\Exception $e) {
            DB::rollBack();
            return $e;
        }


    }

    public function delete($id){

    }
}
