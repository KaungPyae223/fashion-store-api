<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Repositories\Contract\BaseRepository;
use App\Repositories\Contract\BasicFunctions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BasicFunctions implements BaseRepository
{

    protected $model;

    function __construct()
    {
        $this->model = Order::class;

    }

    public function find($id){
        return $this->model::find($id);
    }

    public function create(array $data){

        try{

            DB::beginTransaction();

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

            $orderDetailsData = [];
            foreach ($data["orderDetails"] as $orderProduct) {

                $orderDetailsData[] = [
                    'order_id' => $order->id,
                    'product_id' => $orderProduct['product_id'],
                    'size' => $orderProduct['size'],
                    'unit_price' => $orderProduct['unit_price'],
                    'qty' => $orderProduct['qty'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderDetails::insert($orderDetailsData);

            DB::commit();

            return $order;


        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    public function update(array $data){

        $order = $this->find($data["id"]);
        $admin_id = Auth::user()->admin->id;

        $order->update([
            "delivery_id" => $data["delivery_id"],
            "admin_id" => $admin_id,
            "status" => "delivered"
        ]);

        return $order;
    }

    public function delete($id){

    }
}
