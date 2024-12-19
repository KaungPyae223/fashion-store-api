<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            "user_id" => $this->id,
            "name" => $this->user->name,
            "email" => $this->user->email,
            "role" => $this->user->role,
            "phone" => $this->phone,
            "address" => $this->address,
            "photo" => $this->photo,
            "retired" => $this->retired
        ];
    }
}
