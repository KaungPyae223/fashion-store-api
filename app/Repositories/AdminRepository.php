<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\User;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRepository extends BasicFunctions implements BaseRepository {

    protected $userModel;
    protected $adminModel;
    protected $admin_id;

    function __construct()
    {
        $this->userModel = User::class;
        $this->adminModel = Admin::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->adminModel::find($id);
    }

    public function findUser($id){
        return $this->userModel::find($id);
    }




    public function create(array $data){

        $imageURL = $this->storePhoto($data["photo"],"adminImage");

        try{

            DB::beginTransaction();

            $user = $this->userModel::create([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => $data["password"],
                "role" => $data["role"],
            ]);


            $admin = $this->adminModel::create([
                "user_id" => $user->id,
                "phone" => $data["phone"],
                "address" => $data["address"],
                "photo" => $imageURL
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Admin",
                "action" => "Create a new admin ".$data["name"]
            ]);

            DB::commit();

            return $admin;

        }catch (\Exception $e) {

            $this->deletePhoto($imageURL);

            DB::rollBack();
            return $e;

        }


    }

    public function update(array $data){

        try{

            DB::beginTransaction();

            $admin = $this->find($data["id"]);
            $admin -> update([
                "phone" => $data["phone"],
                "address" => $data["address"],
                "retired" => $data["retired"]
            ]);

            $user = $this->findUser($admin->user_id);
            $user -> update([
                "name" => $data["name"],
                "role" => $data["role"],
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Admin",
                "action" => "Update a admin ".$data["name"]. " data"
            ]);

            DB::commit();

            return $admin;


        }catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }


    }

    public function updatePhoto(array $data){

        $admin = $this->find($data["id"]);

        $this->deletePhoto($admin->photo);

        $newPhotoURL = $this->storePhoto($data["photo"],"adminImage");

        $admin -> update ([
            "photo" => $newPhotoURL
        ]);

        $this->addAdminActivity([
            "admin_id" => $this->admin_id,
            "method" => "Update",
            "type" => "Admin",
            "action" => "Update a admin ".$admin->user->name. " photo"
        ]);

        return $admin;

    }

    public function delete($id){

    }

}
