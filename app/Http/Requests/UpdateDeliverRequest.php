<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliverRequest extends FormRequest
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
            "name" => "required|string|unique:delivers,name," . $this->route('deliver'),
            "email" => "required|email|unique:delivers,email," . $this->route('deliver'),
            "phone" => "required|string",
            "address" => "required|string",
            "status" => "required|string|in:available,unavailable",
        ];
    }
}
