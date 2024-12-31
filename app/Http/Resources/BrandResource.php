<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            "name" => $this->name,
            "photo" => $this->photo,
            "total_products" => $this->product->count(),
            "relative_category" =>
            $this->product->map(function($product){
                return [
                    "id" => $product->category->id,
                    "category" => $product->category->category
                ];
            })->unique("category")->values(),
            "relative_type" =>
            $this->product->map(function($product){
                return [
                    "id" => $product->type->id,
                    "type" => $product->type->type
                ];
            })->unique("type")->values(),
        ];
    }
}
