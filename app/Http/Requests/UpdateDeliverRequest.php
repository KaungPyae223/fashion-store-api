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
            "admin_id" => ["required","integer","exists:admins,id"],
            "name" => ["required","string","unique:delivers,name,".$this->id],
            "email" => ["required","email"],
            "phone" => ["required","string"],
            "address" => ["required","string"],
            "status" => ["required","string","in:available,unavailable"]
        ];
    }
}
