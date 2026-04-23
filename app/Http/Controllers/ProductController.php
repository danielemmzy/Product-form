<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Show page
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'asc')->get();
        return view('products', compact('products'));
    }

    /**
     * Store product via AJAX
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product = Product::create([
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        $this->service->syncToJson();

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    /**
     * Update product via AJAX
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        $this->service->syncToJson();

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }
}
