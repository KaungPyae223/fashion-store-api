<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

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

    public function index()
    {
        //
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

        return new PaymentResource($payment);

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
            "admin_id" => $request->admin_id,
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
    }
}
