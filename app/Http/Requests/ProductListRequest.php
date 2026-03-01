<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductListRequest extends FormRequest
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
            // Filtros de preço
            'price_min'  => ['nullable', 'numeric', 'min:0'],
            'price_max'  => ['nullable', 'numeric', 'gte:price_min'], // Garante que o max seja maior ou igual ao min

            // Filtros de Texto e Categoria
            'category'   => ['nullable', 'string'],
            'search'     => ['nullable', 'string', 'min:3'], // Busca por título parcial, mínimo de 3 caracteres por bom senso de performance

            // Filtro de Rating
            'rating_min' => ['nullable', 'numeric', 'min:0', 'max:5'],

            // Paginação
            'page'       => ['nullable', 'integer', 'min:1'],
            'per_page'   => ['nullable', 'integer', 'min:1', 'max:100'], // Regra: limitar page size máximo 100

            // Ordenação
            'sort_by'    => ['nullable', 'string', 'in:price,title,rating_rate'], 
            'sort_dir'   => ['nullable', 'string', 'in:asc,desc'], // Direção da ordenação
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'price_max.gte'    => 'O preço máximo deve ser maior ou igual ao preço mínimo.',
            'rating_min.max'   => 'A avaliação mínima não pode ser superior a 5.',
            'per_page.max'     => 'O limite máximo de itens por página é 100.',
            'sort_by.in'       => 'A ordenação deve ser por price, title ou rating_rate.',
        ];
    }
}
