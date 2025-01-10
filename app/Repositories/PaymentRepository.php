<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Payment::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }


    public function create(array $data){


        try{

            DB::beginTransaction();

            $payment = $this->model::create([
                "payment" => $data["payment"],
            ]);

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Create",
                "type" => "Payment",
                "action" => "created payment ".$data["payment"]
            ]);

            DB::commit();

            return $payment;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

    public function update(array $data){

        $payment = $this->find($data["id"]);

        try{

            DB::beginTransaction();

            $this->addAdminActivity([
                "admin_id" => $this->admin_id,
                "method" => "Update",
                "type" => "Payment",
                "action" => "Update a payment ". $data["id"].
                $this->compareDiff("payment",$payment->payment,$data["payment"]).
                $this->compareDiff("status",$payment->status,$data["status"])
            ]);

            $payment->update([
                "payment" => $data["payment"],
                "status" => $data["status"]
            ]);

            DB::commit();

            return $payment;

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;
        }



    }

    public function delete($id){
        $payment = $this->find($id);
        $payment->delete();
    }

}
