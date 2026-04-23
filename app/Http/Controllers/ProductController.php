<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

/**
 * Handles HTTP requests only.
 * Business logic is delegated to ProductService.
 */
class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Load main page with products
     */
    public function index()
    {
        $products = $this->service->getAll();

        return view('products', compact('products'));
    }

    /**
     * Store new product via AJAX
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'quantity'     => 'required|integer|min:0',
            'price'        => 'required|numeric|min:0',
        ]);

        $product = [
            'id'           => time(),
            'product_name' => $request->product_name,
            'quantity'     => (int) $request->quantity,
            'price'        => (float) $request->price,
            'total'        => $request->quantity * $request->price,
            'created_at'   => now()->format('Y-m-d H:i:s'),
        ];

        $products = $this->service->add($product);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Update product via AJAX
     */
    public function update(Request $request, $id)
    {
        $data = [
            'product_name' => $request->product_name,
            'quantity'     => (int) $request->quantity,
            'price'        => (float) $request->price,
            'total'        => $request->quantity * $request->price,
        ];

        $products = $this->service->update($id, $data);

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
