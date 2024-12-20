<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMonitoringResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->admin->user->name,
            "photo" => $this->admin->photo,
            "email" => $this->admin->user->email,
            "role" => $this->admin->user->role,
            "method" => $this->method,
            "type" => $this->type,
            "action" => $this->action,
            "time" => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
