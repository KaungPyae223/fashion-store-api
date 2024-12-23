<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;

class PaymentRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = Payment::class;
    }

    public function find($id){
        return $this->model::find($id);
    }


    public function create(array $data){

        $payment = $this->model::create([
            "payment" => $data["payment"],
        ]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Create",
            "type" => "Payment",
            "action" => "created payment ".$data["payment"]
        ]);

        return $payment;

    }

    public function update(array $data){

        $payment = $this->find($data["id"]);

        $this->addAdminActivity([
            "admin_id" => $data["admin_id"],
            "method" => "Create",
            "type" => "Payment",
            "action" => "Update a payment ". $data["id"].
            $this->compareDiff("payment",$payment->payment,$data["payment"]).
            $this->compareDiff("status",$payment->status,$data["status"])
        ]);

        $payment->update([
            "payment" => $data["payment"],
            "status" => $data["status"]
        ]);

        return $payment;

    }

    public function delete($id){

    }

}
