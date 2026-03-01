<?php

namespace App\Http\Controllers;

use App\Models\Product;

use App\Http\Resources\ProductResource;

use App\Http\Requests\ProductListRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductDeleteRequest;

use App\Services\ProductListService;
use App\Services\ProductDeleteService;
use App\Services\ProductUpdateService;

use InvalidArgumentException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Listagem de produtos com filtros, ordenação e paginação.
     */
    public function index(
        ProductListRequest $request,
        ProductListService $listService
    ): AnonymousResourceCollection {

        $filters = $request->validated();

        $products = $listService->list($filters);

        return ProductResource::collection($products);

    }

    /**
     * Detalhes do produto
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(
        ProductUpdateRequest $request,
        Product $product,
        ProductUpdateService $updateService
    ) {
        $validatedData = $request->validated();
        $updatedProduct = $updateService->update($product, $validatedData);

        return new ProductResource($updatedProduct);
    }

    /**
     * Remoção do produto (Soft Delete)
     */
    public function destroy(
        ProductDeleteRequest $request,
        Product $product,
        ProductDeleteService $deleteService
    ) {
        try {
            $reason = $request->validated('reason');

            $deleteService->delete($product, $reason); // Passa para o Service executar as regras

            return response()->json([
                'message' => 'Produto removido com sucesso.'
            ], 200);
        } catch (InvalidArgumentException $e) { // Se o rating for > 4.5
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
