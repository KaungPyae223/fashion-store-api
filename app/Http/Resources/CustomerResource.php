<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "name" => $this->user->name,
            "email" => $this->user->email,
            "phone" => $this->phone,
            "city" => $this->city,
            "township" => $this->township,
            "address" => $this->address,
            "zip_code" => $this->zip_code,
        ];
    }
}
