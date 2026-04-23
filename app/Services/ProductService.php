<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/** 
 * Handles all product storage logic using a JSON file.
 */
class ProductService
{
    private string $file = 'products.json';

    /**
     * Get all products from storage
     */
    public function getAll(): array
    {
        if (!Storage::exists($this->file)) {
            return [];
        }

        return json_decode(Storage::get($this->file), true);
    }

    /**
     * Save full product array to file
     */
    public function save(array $products): void
    {
        Storage::put(
            $this->file,
            json_encode($products, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Add a new product
     */
    public function add(array $product): array
    {
        $products = $this->getAll();
        $products[] = $product;

        $this->save($products);

        return $products;
    }

    /**
     * Update a product by ID
     */
    public function update($id, array $data): array
    {
        $products = $this->getAll();

        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product = array_merge($product, $data);
                $product['total'] = $product['quantity'] * $product['price'];
            }
        }

        $this->save($products);

        return $products;
    }
}