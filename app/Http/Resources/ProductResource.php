<?php

namespace App\Http\Resources;

use App\Traits\AllTraits;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use AllTraits;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name'=> $this->name,
            'thumbnail'=> $this->fullImageUrlForApi($this->thumbnail),
            'description'=> $this->description,
            'price'=> (string)$this->price,
            'stock'=> $this->stock,
            'status'=> $this->status,
            'images' => ProductImageResource::collection($this->images),
        ];
    }
}
