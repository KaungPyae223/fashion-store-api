<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            "customer_id" => "required|integer|exists:customers,id",
            "payment_id" => "required|integer|exists:payments,id",
            "total_products" => "required|integer",
            "sub_total" => "required|integer",
            "tax" => "required|integer",
            "total_qty" => "required|integer",
            "total_price" => "required|integer",
            "name" => "required|string",
            "email" => "required|email",
            "phone" => "required|string",
            "address" => "required|string",
            "note" => "string",
        ];
    }
}
