<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreCustomerQuestionRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Mail\AskMail;
use App\Models\Customer;
use App\Models\CustomerQuestion;
use App\Models\Order;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

     public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 401);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        $user->tokens()->delete();

        return response()->json(['message' => 'Password changed successfully']);
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

        Mail::to($request->user()->email)->send(new AskMail());


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


     public function wishList (Request $request) {

        $wishlist = $request->user()->customer->wishlist;

        return response()->json($wishlist->map(function($wishList){

            $discount_price = 0;
            $discount_percent = 0;

            $profit = $wishList->product->price * ($wishList->product->profit_percent / 100);

            $originalSellPrice = $profit + $wishList->product->price;

            $start_date = $wishList->product->discount_start;



            if ($start_date && $start_date < now()) {

                $discount_percent = $wishList->product->profit_percent;

                $discount_price = $wishList->product->price * ($discount_percent / 100);
            }

            return [
                "id" => $wishList->id,
                "discount_price" => $discount_price,
                "discount_percent" => $discount_percent,
                "product_id" => $wishList->product_id,
                "image" => $wishList->product->cover_photo,
                "name" => $wishList->product->name,
                "color" => $wishList->product->color->color,
                "price" => $originalSellPrice,
            ];
        }));

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


        return response()->json([
            "customer" => [
                "name" => $customer->user->name,
                "email" => $customer->user->email,
                "phone" => $customer->phone,
                "address" => $customer->address,
                "total_orders" => $customer->orders->count()
            ]

        ]);

    }

    public function customerOrder (Request $request, $id){
        $customer = Customer::find($id);

        $time = $request->input("time");

        $customer_orders = $customer->orders()->orderBy("id", "desc");

        if($time){
            $customer_orders->where("created_at",$time);
        }

        $customer_orders = $customer_orders->paginate(10);

        return response()->json([
            "data" => $customer_orders->map(function($data){
                return [
                    "id" => $data->id,
                    "payment_method" => $data->payment->payment,
                    "total_products" => $data->total_products,
                    "total_qty" => $data->total_qty,
                    "tax" => $data->tax,
                    "amount" => $data->total_price,
                    "status" => $data->status,
                    "date" => $data->created_at
                ];
            }),
            'meta' => [
                'current_page' => $customer_orders->currentPage(),
                'last_page' => $customer_orders->lastPage(),
                'total' => $customer_orders->total(),
            ],
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
