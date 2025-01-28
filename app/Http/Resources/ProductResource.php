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

        $discount_price = 0;
        $discount_percent = 0;

        $profit = $this->price * ($this->profit_percent/100);

        $sell_price = $profit * $this->price;

        $start_date = $this->discount_start;



        if($start_date && $start_date < now()){

            $discount_percent = $this->profit_percent;

            $discount_price = $this->price * ($discount_percent/100);

            $sell_price = $sell_price - $discount_price;

        }

        return [
            "id" => $this->id,
            "cover_image" => $this->cover_photo,
            "name" => $this->name,
            "original_price" => $this->price,
            "profit_percent" => $this->profit_percent,
            "discount_price" => $discount_price,
            "sell_price" => $this->price + ($this->price * ($this->profit_percent / 100)) - $discount_price,
            "status" => $this->status,
            "gender" => $this->gender,
            "category" => $this->category->category,
            "brand" => $this->brand->name,
            "type" => $this->type->type,
            "totalQty" => $this->product_size->sum('qty'),
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
