<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\AllTraits;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    use AllTraits;

    public function list(int $perPage = 12): LengthAwarePaginator
    {
        return Product::orderBy('created_at','desc')->paginate($perPage);
    }

    public function create(array $data)
    {
        // Upload thumbnail
        $thumbnail = $this->uploadToPublic($data['thumbnail'], 'uploads/products/thumbnail');
        $data['thumbnail'] = $thumbnail[0] ?? null;

        $product = Product::create($data);

        // Handle images
        if (!empty($data['images'])) {
            $uploaded = $this->uploadToPublic($data['images'], 'uploads/products/images');

            foreach ($uploaded as $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                ]);
            }
        }

        return $product->load('images');
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function update(Product $product, array $data)
    {
        // Upload thumbnail
        if (!empty($data['thumbnail'])) {
            $thumbnail = $this->uploadToPublic($data['thumbnail'], 'uploads/products/thumbnail');
        }

        $product->update([
            'name' => $data['name'],
            'thumbnail' => $thumbnail[0] ?? $product->thumbnail,
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'status' => $data['status'],
        ]);

        // If new images uploaded
        if (!empty($data['images'])) {

            // Delete old images files
            foreach ($product->images as $img) {
                $full = public_path($img->image);
                if (file_exists($full)) unlink($full);
                $img->delete();
            }

            // Upload new images
            $uploaded = $this->uploadToPublic($data['images'], 'uploads/products/images');

            foreach ($uploaded as $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $path,
                ]);
            }
        }

        return $product->load('images');
    }

    public function delete(Product $product)
    {
        $this->deleteFromPublic($product, 'image');
        return $product->delete();
    }

}
