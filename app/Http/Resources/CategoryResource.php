<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "category" => $this->category,
            "brands" => $this->product
                ->pluck('brand.name') // Directly pluck the brand names
                ->unique() // Ensure distinct brand names
                ->values(),
            "types" => $this->type->pluck('type')->unique()->values(),
            "sizes" => $this->size->pluck('size')->unique()->values(),
            "total_products" => $this->product->count()
        ];
    }
}
