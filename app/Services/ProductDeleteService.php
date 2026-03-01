<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

class ProductDeleteService
{
    /**
     * Valida as regras de negócio e aplica o soft delete.
     */
    public function delete(Product $product, string $reason): void
    {
        // Regra: não permitir remover produto com rating > 4.5 
        if ($product->rating_rate > 4.5) {
            throw new InvalidArgumentException('Produtos com avaliação superior a 4.5 não podem ser removidos.');
        }

        // Regra: deve registrar motivo da remoção 
        $currentLog = $product->update_log ?? [];
        
        // Adiciona o evento de deleção no histórico
        array_unshift($currentLog, [
            'action'     => 'deleted',
            'deleted_at' => now()->toDateTimeString(),
            'reason'     => $reason
        ]);

        $product->update_log = $currentLog;
        $product->save();

        // O Laravel vai preencher a coluna 'deleted_at' automaticamente e ocultar o registro
        $product->delete();
    }
}