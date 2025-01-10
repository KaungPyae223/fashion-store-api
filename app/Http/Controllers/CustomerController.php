<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $customerRepository;

     function __construct(CustomerRepository $customerRepository)
     {
        $this->customerRepository = $customerRepository;
     }

    public function index(Request $request)
    {

        $searchTerm = $request->input('q');


        $query = Customer::query();

        if($searchTerm) {
            $query->whereHas(
                'user', function ($q) use ($searchTerm) {$q->where('name', 'like', '%' . $searchTerm . '%')->orWhere('email', 'like', '%' . $searchTerm . '%');})
                ->orWhere('phone', 'like', '%' . $searchTerm . '%');
        }

        $product = $query->paginate(10);

        $data = CustomerResource::collection($product);

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $product->currentPage(),
                'last_page' => $product->lastPage(),
                'total' => $product->total(),
            ],
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
    public function store(StoreCustomerRequest $request)
    {


    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function customerDetails($id){

        $customer = Customer::find($id);

        $customer_orders = $customer->orders()->get();

        return response()->json([
            "customer" => [
                "name" => $customer->user->name,
                "email" => $customer->user->email,
                "phone" => $customer->phone,
                "address" => $customer->address,
                "city" => $customer->city,
                "township" => $customer->township,
                "zipCode" => $customer->zip_code
            ],
            "customer_orders" => $customer_orders->map(function($order){
                return [
                    "id" => $order->id,
                    "date" => $order->created_at,
                    "total_qty" => $order->total_qty,
                    "total_products" => $order->orderDetails()->count(),
                    "total_amount" => $order->total_price,
                    "tax" => $order->tax,
                    "payment" => $order->payment->payment,
                    "status" => $order->status,
                ];
            })
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer = $this->customerRepository->update([
            "name" => $request->name,
            "phone" => $request->phone,
            "city" => $request->city,
            "township" => $request->township,
            "zip_code" => $request->zip_code,
            "address" => $request->address,
            "user_id" => $request->user_id,
            "id" => $request->id
        ]);

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {

    }
}
