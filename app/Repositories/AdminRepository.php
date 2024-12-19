<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\User;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class AdminRepository extends BasicFunctions implements BaseRepository {

    protected $userModel;
    protected $adminModel;

    function __construct()
    {
        $this->userModel = User::class;
        $this->adminModel = Admin::class;
    }

    public function find($id){
        return $this->adminModel::find($id);
    }

    public function findUser($id){
        return $this->userModel::find($id);
    }

    public function storePhoto($image){

        $imageName = 'Admin_Image' . uniqid() . '.' . $image->extension();
        $imagePath = $image->storeAs("images/adminImage", $imageName,"public");
        return  asset('storage/' . $imagePath);

    }


    public function create(array $data){

        $user = $this->userModel::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => $data["password"],
            "role" => $data["role"],
        ]);

        $imageURL = $this->storePhoto($data["photo"]);

        $admin = $this->adminModel::create([
            "user_id" => $user->id,
            "phone" => $data["phone"],
            "address" => $data["address"],
            "photo" => $imageURL
        ]);

        return $admin;

    }

    public function update(array $data){
        $user = $this->findUser($data["user_id"]);
        $user -> update([
            "name" => $data["name"],
            "role" => $data["role"],
        ]);

        $admin = $this->find($data["id"]);
        $admin -> update([
            "user_id" => $user->id,
            "phone" => $data["phone"],
            "address" => $data["address"],
            "retired" => $data["retired"]
        ]);

        return $admin;

    }

    public function updatePhoto(array $data){

        $admin = $this->find($data["id"]);

        $this->deletePhoto($admin->photo);

        $newPhotoURL = $this->storePhoto($data["photo"]);

        $admin -> update ([
            "photo" => $newPhotoURL
        ]);

        return $admin;

    }

    public function delete($id){

    }

}
