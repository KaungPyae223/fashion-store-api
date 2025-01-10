<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "cover_image" => $this->cover_photo,
            "name" => $this->name,
            "price" => $this->price,
            "status" => $this->status,
            "gender" => $this->gender,
            "category" => $this->category->category,
            "brand" => $this->brand->name,
            "type" => $this->type->type,

            "color" => $this->color->color,
            "sizes" => $this->size->map(function($size){
               return [
                    "id" => $size->id,
                    "size" => $size->size
                ];
            })
        ];
    }
}
