<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
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
            "category_id" => $this->category_id,
            "total_products" => $this->product->count(),
            "type" => $this->type,
            "gender" => $this->gender,
            "relative_category" => $this->category->category,
            "relative_brand" => $this->product
                ->map(function($product) {
                    return [
                        "brandName" => $product->brand->name,
                        "brandID" => $product->brand->id,
                    ];
                })
                ->unique('brandID') // Ensure distinct brands
                ->values()
        ];
    }
}
