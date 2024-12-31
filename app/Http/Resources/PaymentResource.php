<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" =>$this->id,
            "payment" =>$this->payment,
            "status" =>$this->status,
            "total_payments" =>$this->order->count(),
            "total_amount" =>$this->order->sum("total_price")


        ];
    }
}
