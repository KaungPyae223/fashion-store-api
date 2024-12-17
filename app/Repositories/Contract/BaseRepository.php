<?php

namespace App\Repositories\Contract;

interface BaseRepository {

    public function find($id);

    public function create(array $data);

    public function update(array $data);

    public function delete($id);

}
