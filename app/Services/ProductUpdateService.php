<?php

namespace App\Services;

use App\Models\Product;

class ProductUpdateService
{
    /**
     * Atualiza o produto e registra o log das alterações.
     */
    public function update(Product $product, array $data): Product
    {
        // Preenche o model com os novos dados, mas ainda não salva no banco
        $product->fill($data);

        // Verifica se houve alguma alteração real nos dados permitidos
        if ($product->isDirty()) {
            
            // Pega apenas os campos que foram modificados
            $changes = $product->getDirty();
            $original = $product->getOriginal();
            
            // Monta a estrutura do log simples da alteração
            $logEntry = [
                'updated_at' => now()->toDateTimeString(),
                'fields'     => []
            ];

            foreach ($changes as $column => $newValue) {
                // Evita logar a própria coluna de log caso ela apareça
                if ($column !== 'update_log') {
                    $logEntry['fields'][$column] = [
                        'from' => $original[$column] ?? null,
                        'to'   => $newValue
                    ];
                }
            }

            // Recupera o log atual
            $currentLog = $product->update_log ?? [];
            
            // Adiciona a nova entrada de log no início do array
            array_unshift($currentLog, $logEntry);
            
            // Atribui o novo log ao model
            $product->update_log = $currentLog;

            // Salva as alterações e o log no banco de dados em uma única query
            $product->save();
        }

        return $product;
    }
}