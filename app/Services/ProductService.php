<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

/**
 * Handles syncing DB data into JSON file
 */
class ProductService
{
    public function syncToJson(): void
    {
        $products = Product::orderBy('created_at', 'asc')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'product_name' => $p->product_name,
                'quantity' => $p->quantity,
                'price' => $p->price,
                'total_value' => $p->quantity * $p->price,
                'submitted_at' => $p->created_at->format('Y-m-d H:i:s'),
            ];
        });

        Storage::put(
            'products.json',
            json_encode($products, JSON_PRETTY_PRINT)
        );
    }
}