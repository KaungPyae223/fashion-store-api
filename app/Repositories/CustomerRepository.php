<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contract\BaseRepository;

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
        $user = $this->findUser($data["user_id"]);
        $user->update(["name" => $data["name"]]);

        $customer = $this->find($data["id"]);
        $customer->update([
            "phone" => $data["phone"],
            "city" => $data["city"],
            "township" => $data["township"],
            "zip_code" => $data["zip_code"],
            "address" => $data["address"],
        ]);

        return $customer;
    }

    public function delete($id){

    }
}
