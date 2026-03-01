<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Exige que um motivo seja enviado para cumprir a regra de negócio
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'É obrigatório informar o motivo da remoção.',
            'reason.min'      => 'O motivo da remoção deve ter pelo menos 5 caracteres.',
        ];
    }
}
