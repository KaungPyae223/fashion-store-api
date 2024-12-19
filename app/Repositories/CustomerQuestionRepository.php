<?php

namespace App\Repositories;

use App\Models\CustomerQuestion;

class CustomerQuestionRepository {

    protected $model;

    function __construct()
    {
        $this->model = CustomerQuestion::class;
    }

    public function find($id) {
        return $this->model::find($id);
    }

    public function askQuestion (array $data){
        $question = $this->model::create($data);
        return $question;
    }

    public function answerQuestion (array $data){

        $question = $this->find($data["id"]);
        $question->update([
            "admin_id" => $data["admin_id"],
            "answer" => $data["answer"]
        ]);

        return $question;
    }

}
