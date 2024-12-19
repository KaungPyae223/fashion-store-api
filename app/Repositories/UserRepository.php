<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contract\BaseRepository;


class UserRepository implements BaseRepository {

    protected $model;

    function __construct()
    {
        $this->model = User::class;
    }

    public function find($id){

    }

    public function create(array $data){
        $user = $this->model::create($data);
        return $user;
    }


    public function update(array $data){

    }

    public function delete($id){

    }


}
