<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly int $external_id,
        public readonly string $title,
        public readonly float $price,
        public readonly string $description,
        public readonly string $category,
        public readonly ?string $image,
        public readonly float $rating_rate,
        public readonly int $rating_count
    ) {}

    /**
     * Fabric para criar o DTO a partir do retorno da FakeStoreAPI
     */
    public static function fromAPI(array $data): self
    {
        return new self(
            external_id: $data['id'],
            title: $data['title'],
            price: (float) $data['price'],
            description: $data['description'],
            category: $data['category'],
            image: $data['image'] ?? null,
            rating_rate: (float) ($data['rating']['rate'] ?? 0),
            rating_count: (int) ($data['rating']['count'] ?? 0),
        );
    }

    /**
     * Converte o DTO para array pronto para o Eloquent 
     */
    public function toArray(): array
    {
        return [
            'external_id' => $this->external_id,
            'title'       => $this->title,
            'price'       => $this->price,
            'description' => $this->description,
            'category'    => $this->category,
            'image'       => $this->image,
            'rating_rate' => $this->rating_rate,
            'rating_count'=> $this->rating_count,
        ];
    }
}