<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use App\DTOs\ProductDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductImportService
{
    protected string $apiUrl;
    protected mixed $verifySsl;

    public function __construct()
    {
        $this->apiUrl = config('services.fakestore.url');
        $this->verifySsl = config('services.fakestore.verify_ssl', true);

        if (app()->environment('production')) {
            $this->verifySsl = true;
        }
    }

    public function import(): array
    {
        $stats = [
            'imported' => 0,
            'updated'  => 0,
            'skipped'  => 0,
        ];

        try {
            $response = Http::withOptions(['verify' => $this->verifySsl])
                            ->retry(3, 100)
                            ->get($this->apiUrl);

            if ($response->failed()) {
                Log::error('Falha na integração com FakeStoreAPI', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'A API externa está indisponível no momento.',
                ];
            }

            $productsData = $response->json();

            foreach ($productsData as $data) {
                $dto = ProductDTO::fromApi($data);

                // Utilizando o withTrashed() para evitar o erro 503 com itens deletados
                $product = Product::withTrashed()->firstOrNew(['external_id' => $dto->external_id]);
                
                $exists = $product->exists;
                $wasTrashed = $product->trashed(); // Verificar se o produto estava na lixeira antes de preencher

                $product->fill($dto->toArray());

                // Força a entrada se os dados mudaram OU se o produto estava deletado
                if ($product->isDirty() || $wasTrashed) {
                    
                    if ($exists) {
                        $changes = $product->getDirty();
                        $original = $product->getOriginal();
                        
                        $logEntry = [
                            'action'     => 'api_sync',
                            'updated_at' => now()->toDateTimeString(),
                            'fields'     => []
                        ];

                        // Se estava na lixeira, adiciona no log que ele foi restaurado
                        if ($wasTrashed) {
                            $logEntry['fields']['status'] = [
                                'from' => 'deleted',
                                'to'   => 'restored'
                            ];
                            // Tira da lixeira em memória para salvar junto com as outras alterações
                            $product->deleted_at = null;
                        }

                        foreach ($changes as $column => $newValue) {
                            if ($column !== 'update_log' && $column !== 'deleted_at') {
                                $logEntry['fields'][$column] = [
                                    'from' => $original[$column] ?? null,
                                    'to'   => $newValue
                                ];
                            }
                        }

                        // Apenas salva o log se captura alguma alteração de campo ou a restauração
                        if (!empty($logEntry['fields'])) {
                            $currentLog = $product->update_log ?? [];
                            array_unshift($currentLog, $logEntry);
                            $product->update_log = $currentLog;
                        }
                    }

                    $product->save();

                    // Limpa o cache das estatísticas para manter os números em tempo real
                    Cache::forget('products_stats');
                    
                    if ($exists) {
                        $stats['updated']++; 
                    } else {
                        $stats['imported']++;
                    }
                } else {
                    $stats['skipped']++;
                }
            }

            Log::info('Importação de produtos finalizada com sucesso', $stats);

            return [
                'success' => true,
                'data' => $stats
            ];

        } catch (Exception $e) {
            Log::error('Exceção crítica ao integrar com FakeStoreAPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Ocorreu um erro interno durante a comunicação com a API.',
            ];
        }
    }
}