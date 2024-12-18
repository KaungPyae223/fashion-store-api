<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contract\BaseRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserRepository implements BaseRepository {

    protected $model;

    function __construct()
    {
        $this->model = User::class;
    }

    public function find($id){
        $user = $this->model::find($id);
        return $user;
    }

    public function create(array $data){
        $user = $this->model::create($data);
        return $user;
    }


    public function update(array $data){

    }

    public function delete($id){

    }

   

    public function updateName(array $data){
        $user = $this->find($data["id"]);
        $user->name = $data["name"];
        $user -> update();

        return $user;
    }

}
