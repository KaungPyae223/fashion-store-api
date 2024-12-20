<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class BlogRepository extends BasicFunctions implements BaseRepository{

    protected $model;

    function __construct()
    {
        $this->model = Blog::class;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        $imageURL = $this->storePhoto($data["photo"],"blogImage");

        $blog = $this->model::create([

            "admin_id" => $data["admin_id"],
            "title" => $data["title"],
            "photo" => $imageURL,
            "content" => $data["content"]

        ]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Create",
            "type" => "Blog",
            "action" => "Create a blog ".$data["title"]
        ]);

        return $blog;

    }

    public function update(array $data){

        $blog = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Blog",
            "action" =>
                "Update a blog ".$data["id"]." data ".
                $this->compareDiff("title",$blog["title"],$data["title"])
        ]);

        $blog->update([
            $data
        ]);

        return $blog;

    }

    public function updatePhoto(array $data){

        $blog = $this->find($data["id"]);

        $this->deletePhoto($blog->photo);

        $imageURL = $this->storePhoto($data["photo"],"blogImage");

        $blog->update([
            "photo" => $imageURL
        ]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Update",
            "type" => "Blog",
            "action" => "Update a blog ". $blog->name . " photo"
        ]);

        return $blog;

    }

    public function delete($id){

        $blog = $this->find($id);

        $this->deletePhoto($blog->photo);

        $blog->delete();

        return response()->json(
            [
                "message" => "successfully delete the blog",
                "status" => 200
            ]
        );
    }

}
