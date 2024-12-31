<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $paymentRepository;

     function __construct(PaymentRepository $paymentRepository)
     {
        $this->paymentRepository = $paymentRepository;
     }

    public function index(Request $request)
    {
        $searchTerm = $request->input('q');


        $query = Payment::query();

        if ($searchTerm) {
            $query->where('payment', 'like', '%' . $searchTerm . '%');
        }

        // Paginate the results
        $payments = $query->orderBy("id", "desc")->paginate(10);

        // Transform the paginated data using the resource collection
        $data = PaymentResource::collection($payments);

        // Return the response with meta information
        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
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
    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentRepository->create([
            "payment" => $request->payment,
            "admin_id" => $request->admin_id
        ]);

        return response()->json([
            'message' => 'Size created successfully',
            'data' => new PaymentResource($payment)
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, $id)
    {
        $payment = $this->paymentRepository->update([
            "payment" => $request->payment,
            "id" => $id,
            "status" => $request->status
        ]);

        return new PaymentResource($payment);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //

        $count = $payment->order->count();

        if($count == 0){
            $this->paymentRepository->delete($payment->id);
            return response()->json(['message' => 'Payment deleted successfully']);
        }else{
            return response()->json([
                "status" => 409,
                "error" => "Conflict",
                "message" => "Payment cannot be deleted due to existing dependencies."
            ],409);
        }

    }
}
