<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            // Pode alterar: title, price, category
            'title'    => ['sometimes', 'required', 'string', 'min:3'], // title mínimo 3 caracteres 
            'price'    => ['sometimes', 'required', 'numeric', 'gt:0'], // price > 0 
            'category' => ['sometimes', 'required', 'string'],
        ];
    }
}
