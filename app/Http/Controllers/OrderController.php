<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Deliver;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $orderRepository;

     function __construct(OrderRepository $orderRepository)
     {
         $this->orderRepository = $orderRepository;
     }

    public function index()
    {
        $totalOrderManagers = User::where("role","Order Management")->count();

        $orderManagers = User::where('role', 'Order Management')->get();

        $currentUser = Auth::user();

        $adminRank = $orderManagers->search(function ($admin) use ($currentUser) {
            return $admin->id === $currentUser->id;
        }) + 1;

        $query = Order::query()
        ->where("status","prepare")
        ->where("id", ">=", $adminRank) // Ensure id is at least 2
        ->whereRaw("(id - $adminRank) % $totalOrderManagers = 0")
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $orders = $query->map(function($order){
            return [

                "id" => $order->id,
                "customer_name" => $order->name,
                "address" => $order->address,
                "totalOrders" => $order->total_qty,
                "totalPrice" => $order->total_price,
                "question_at" => Carbon::parse($order->created_at)->diffForHumans(),

            ];
        });


        return response()->json([
            "data" => $orders,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
        ]);

    }

    public function packagingData($id){

        $order = Order::find($id);

        $orderProducts = $order->orderDetails()->get()->map(function($orderDetail){
            return [
                "product_id" => $orderDetail->product_id,
                "product_image" => $orderDetail->product->cover_photo,
                "product_size" => $orderDetail->size,
                "product_name" => $orderDetail->product->name,
                "product_price" => $orderDetail->unit_price,
                "product_qty" => $orderDetail->qty,
                "product_total" => $orderDetail->unit_price * $orderDetail->qty,
            ];
        });


        return response()->json([
            "order_data" => $order,
            "order_products" => $orderProducts,

        ],200);

    }

    public function deliverData(){

        $delivery = Deliver::query()->where("status","available")->get();

        return response()->json([
            "delivery_data" => $delivery,
        ],200);
    }

    public function orderHistory(Request $request)
    {

        $customer = $request->input('customer');
        $admin = $request->input('admin');
        $orderMonth = $request->input('orderMonth');
        $orderID = $request->input('orderId');


        $query = Order::query();

        if ($customer) {
            $query->whereHas('customer.user', function ($q) use ($customer) {
                $q->where('name',"like", "%".$customer."%");
            });
        }

        if ($admin) {
            $query->whereHas('admin.user', function ($q) use ($admin) {
                $q->where('name',"like", "%".$admin."%");
            });
        }

        if ($orderID) {
            $query->where("id", $orderID);
        }

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $query->whereMonth('created_at', '=', date('m', strtotime($orderMonth)))
                  ->whereYear('created_at', '=', date('Y', strtotime($orderMonth)));
        }

        $orders = $query->where("status","delivered")->paginate(10);

        $data = $orders->map(function($order){
            return [
                "id" => $order->id,
                "customer_name" => $order->customer->user->name,
                "address" => $order->address,
                "total_products" => $order->orderDetails()->count(),
                "total_price" => $order->total_price,
                "order_at" => $order->created_at,
                "packager" => $order->admin->user->name,
                "delivery" => $order->delivery->name,

            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);

    }

    public function customerOrder(Request $request){

        $customer = $request->input('customer');
        $orderMonth = $request->input('orderMonth');
        $payment = $request->input('payment');

        $query = Order::query();

        if ($customer) {
            $query->whereHas('customer.user', function ($q) use ($customer) {
                $q->where('name',"like", "%".$customer."%");
            });
        }

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $query->whereMonth('created_at', '=', date('m', strtotime($orderMonth)))
                  ->whereYear('created_at', '=', date('Y', strtotime($orderMonth)));
        }

        if ($payment) {
            $query->whereHas('payment', function ($q) use ($payment) {
                $q->where('payment',"like", "%".$payment."%");
            });
        }

        $orders = $query->paginate(10);


        $data = $orders->map(function($order){
            return [
                "id" => $order->id,
                "customer_name" => $order->customer->user->name,
                "customer_email" => $order->customer->user->email,
                "total_products" => $order->orderDetails()->count(),
                "total_price" => $order->total_price,
                "order_at" => $order->created_at,
                "status" => $order->status,

            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);


    }

    public function orderHistoryDetails($id){

        $order = Order::find($id);

        return response()->json([
            "order_information" => [
                "id" => $order->id,
                "date" => $order->created_at,
                "packager" => $order->admin? $order->admin->user->name : "",
                "packager_id" => $order->admin_id,
                "deliver_date" => $order->updated_at,
                "products" => $order->orderDetails()->count(),
                "order_qty" => $order->total_qty,
                "status" => $order->status,
            ],
            "customer_information" => [
                "name" => $order->customer->user->name,
                "email" => $order->customer->user->email,
                "phone" => $order->customer->phone,
                "address" => $order->customer->address,
            ],
            "delivery_information" => [
                "name" => $order->delivery? $order->delivery->name:null,
                "phone" => $order->delivery? $order->delivery->phone:null,
                "address" => $order->delivery? $order->delivery->address:null,
                "email" => $order->delivery? $order->delivery->email:null,
            ],
            "payment_information" => [
                "method" => $order->payment->payment,
                "total_price" => $order->total_products,
                "tax" => $order->tax,
                "grand_total" => $order->total_price,
            ],
            "receiver_information" => [
                "name" => $order->name,
                "email" => $order->email,
                "phone" => $order->phone,
                "address" => $order->address,
            ],
            "note" => $order->note,
            "order_products" => $order->orderDetails()->get()->map(function($orderDetail){
                return [
                    "id" => $orderDetail->id,
                    "cover_image" => $orderDetail->product->cover_photo,
                    "product_name" => $orderDetail->product->name,
                    "product_size" => $orderDetail->size,
                    "product_color" => $orderDetail->product->color->color,
                    "product_price" => $orderDetail->unit_price,
                    "product_qty" => $orderDetail->qty,
                    "total_price" => $orderDetail->unit_price * $orderDetail->qty,
                ];
            }),

        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderRepository->create($request->validated());

        return response()->json([
            'message' => 'Order successfully',
            'data' => new OrderResource($order),
            "status" => 201
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->orderRepository->update( array_merge($request->validated(),["id" => $id]));
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
