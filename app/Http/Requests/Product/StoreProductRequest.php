<?php

namespace App\Http\Requests\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'thumbnail' => 'required|mimes:jpg,jpeg,png,webp|max:10240',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'sometimes|in:Active,Deactivate',

            // multiple images
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
        ];
    }
}
