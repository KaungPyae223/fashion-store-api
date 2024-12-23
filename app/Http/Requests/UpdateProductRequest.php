<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            "admin_id" => "required|exists:admins,id",
            "type_id" => "required|exists:types,id",
            "brand_id" => "required|exists:brands,id",
            "category_id" => "required|exists:categories,id",
            "color_id" => "required|exists:colors,id",
            "name" => "required",
            "price" => "required|integer",
            "description" => "required",
            "status" => "required|in:public,private",
            "gender" => "required|in:Men,Women,All",
            // "size_id" => "required|array",
        ];
    }
}
