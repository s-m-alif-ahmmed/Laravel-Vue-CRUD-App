<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\AllTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AllTraits;

    protected ProductService $service;

    public function __construct(ProductService $productService)
    {
        $this->service = $productService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 12);

        $products = $this->service->list($perPage);

        return $this->successPagination(
            true,
            'Products retrieved successfully',
            200,
            ProductResource::collection($products),
            true,
            $products
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());
        $data = new ProductResource($product);

        return $this->success(
            'Product saved successfully',
            $data,
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->service->find($id);

        if (!$product) {
            return $this->error('Product not found', 404);
        }

        $data = new ProductResource($product);

        return $this->success(
            'Product retrieved successfully',
            $data,
            200
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->service->update($product, $request->validated());

        return $this->success(
            'Product updated successfully',
            new ProductResource($product),
            200
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);

        return $this->ok(
            'Product deleted successfully',
            200
        );
    }

}
