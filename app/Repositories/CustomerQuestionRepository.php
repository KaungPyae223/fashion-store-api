<?php

namespace App\Repositories;

use App\Models\CustomerQuestion;
use Illuminate\Support\Facades\Auth;

class CustomerQuestionRepository {

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = CustomerQuestion::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id) {
        return $this->model::find($id);
    }



    public function answerQuestion (array $data){

        $question = $this->find($data["id"]);
        $question->update([
            "admin_id" => $this->admin_id,
            "answer" => $data["answer"]
        ]);

        return $question;
    }

}
