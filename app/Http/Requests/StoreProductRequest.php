<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "type_id" => "required|exists:types,id",
            "brand_id" => "required|exists:brands,id",
            "category_id" => "required|exists:categories,id",
            "size_id" => "required",
            "color_id" => "required|exists:colors,id",
            "name" => "required|string",
            "profit_percent" => "required|integer",
            "cover_photo" => "required|image|mimes:jpeg,png,jpg,gif",
            "details_photos.*" => "required|image|mimes:jpeg,png,jpg,gif",
            "price" => "required|integer",
            "description" => "required",
            "status" => "required|in:public,private",
            "gender" => "required|in:Men,Women,All",
        ];
    }
}
