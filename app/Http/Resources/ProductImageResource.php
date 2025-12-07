<?php

namespace App\Http\Resources;

use App\Traits\AllTraits;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    use AllTraits;

    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'image' => $this->fullImageUrlForApi($this->image),
        ];
    }
}
