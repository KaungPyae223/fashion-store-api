<?php

namespace App\Repositories;

use App\Models\Deliver;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;

class DeliverRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Deliver::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){
        $deliver = $this->model::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => $data["address"],
        ]);

        $this->addAdminActivity([
            "admin_id" => $this->admin_id,
            "method" => "Create",
            "type" => "Deliver",
            "action" => "Create a deliver".$data["name"]
        ]);

        return $deliver;
    }

    public function update(array $data){
        $deliver = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $this->admin_id,
            "method" => "Update",
            "type" => "Deliver",
            "action" =>
            "Update a deliver ".
            $this->compareDiff("name",$deliver->name,$data["name"]).
            $this->compareDiff("email",$deliver->email,$data["email"]).
            $this->compareDiff("phone",$deliver->phone,$data["phone"]).
            $this->compareDiff("address",$deliver->address,$data["address"]).
            $this->compareDiff("status",$deliver->status,$data["status"])
        ]);

        $deliver->update([
            "name" => $data["name"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "status" => $data["status"]
        ]);

        return $deliver;
    }

    public function delete($id){
        $deliver = $this->find($id);
        $deliver->delete();
    }
}
