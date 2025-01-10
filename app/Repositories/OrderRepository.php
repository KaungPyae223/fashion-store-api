<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;

class OrderRepository extends BasicFunctions implements BaseRepository
{

    protected $model;
    protected $admin_id;

    function __construct()
    {
        $this->model = Order::class;
        $this->admin_id = Auth::user()->admin->id;
    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){
        $order = $this->model::create([
            "customer_id" => $data["customer_id"],
            "payment_id" => $data["payment_id"],
            "total_products" => $data["total_products"],
            "sub_total" => $data["sub_total"],
            "tax" => $data["tax"],
            "total_qty" => $data["total_qty"],
            "total_price" => $data["total_price"],
            "name" => $data["name"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => $data["address"],
            "note" => $data["note"]
        ]);

        return $order;
    }

    public function update(array $data){

        $order = $this->find($data["id"]);

        $order->update([
            "delivery_id" => $data["delivery_id"],
            "admin_id" => $this->admin_id,
            "status" => "delivered"
        ]);

        return $order;
    }

    public function delete($id){

    }
}
