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
        return [
            'id'             => $this->id,
            'external_id'    => $this->external_id,
            'title'          => $this->title,
            
            // Cast para float para garantir o tipo no JSON
            'price'          => (float) $this->price, 
            
            // Campo calculado exigido: price_with_tax (10%) 
            'price_with_tax' => round($this->price * 1.10, 2), 
            
            'description'    => $this->description,
            'category'       => $this->category,
            'image'          => $this->image,
            
            'rating'         => [
                'rate'  => (float) $this->rating_rate,
                'count' => (int) $this->rating_count,
            ],
            
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
