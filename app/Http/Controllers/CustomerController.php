<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerQuestionRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerQuestion;
use App\Models\Order;
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

     public function customerOrderInformation(Request $request){

        $customer = $request->user()->customer;

        $order = $customer->orders()->where("status","prepare")->get();

        return response()->json($order);


     }

     public function getAllCustomerQuestions(Request $request)
     {

        $id = $request->user()->customer->id;

         $questions = CustomerQuestion::whereNull("answer")
             ->where("customer_id", $id)
             ->orderBy('created_at', 'desc')
             ->get()
             ->map(function ($question) {
                 return [
                     "id" => $question->id,
                     "question" => $question->question,
                     "time" => $question->created_at,
                 ];
             });

         return response()->json([
             "data" => $questions,
             "status" => 200,
         ]);
     }

     public function getAllCustomerAnswers(Request $request)
     {

        $id = $request->user()->customer->id;


         $questions = CustomerQuestion::query()
             ->whereNotNull('answer')
             ->where("customer_id",$id)
             ->orderBy('updated_at', 'desc')
             ->get()
             ->map(function($question){
                 return [
                    "id" => $question->id,
                     "question" => $question->question,
                     "answer" => $question->answer,
                     "question_at" => $question->created_at,
                     "answer_at" => $question->updated_at
                 ];
         });

         return response()->json([
             "data" => $questions,
             "status" => 200,
         ]);

     }


     public function askQuestion(StoreCustomerQuestionRequest $request)
     {
         $question = CustomerQuestion::create([
             "customer_id" => $request->user()->customer->id,
             "question" => $request->question
         ]);

         return response()->json([
             "data" => [
                 "question" => $question->question,
                 "ask_at" => $question->created_at,
             ],
             "status" => 200,
         ]);

     }

     public function customerOrderDetails(Request $request,$id){

        $order = Order::find($id);


        if($order->customer_id != $request->user()->customer->id){
            return response()->json([
                "message" => "unauthorized",
                "status" => 401
            ]);
        }


        return response()->json([
            "order" => [
                "id" => $order->id,
                "name" => $order->customer->user->name,
                "email" => $order->customer->user->email,
                "phone" => $order->customer->phone,
                "order_date" => $order->created_at,
                "receiver_name" => $order->name,
                "receiver_address" => $order->address,
                "receiver_phone" => $order->phone,
                "receiver_email" => $order->email,
                "sub_total" => $order->sub_total,
                "tax" => $order->tax,
                "total_price" => $order->total_price,
                "note" => $order->note,
            ],
            "order_details" => $order->orderDetails->map(function($detail){
                return [
                    "name" => $detail->product->name,
                    "quantity" => $detail->qty,
                    "unit_price" => $detail->unit_price,
                    "size" => $detail->size
                ];
            }),

            "status" => 200
        ]);

     }


     public function customerOrderHistory(Request $request){

        $customer = $request->user()->customer;

        $order = $customer->orders()->where("status","delivered")->get();

        return response()->json($order);


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

    public function getCustomerData(Request $request){
        $user = $request->user();

        return response()->json([
            "id" => $user->customer->id,
            "total_orders" => $user->customer->orders->count(),
            "name" => $user->name,
            "email" => $user->email,
            "address" => $user->customer->address,
            "phone" => $user->customer->phone,
        ]);

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
    public function update (UpdateCustomerRequest $request, Customer $customer)
    {

        $customer = $this->customerRepository->update([
            "name" => $request->name,
            "phone" => $request->phone,
            "address" => $request->address,
            "password" => $request->password,
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
