<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            "name" => "required",
            "email" => "required|unique:users,email",
            "role" => "required",
            "password" => "required|string|min:8",
            "photo" => "required|image|mimes:jpeg,png,jpg,gif",
            "phone" => "required",
            "address" => "required",
        ];
    }
}
