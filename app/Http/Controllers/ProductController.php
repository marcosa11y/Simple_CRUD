<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('categories');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return response()->json($products);
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image'
        ]);

        // create the product first, then handle file upload afterwards so the
        // model instance exists for the relationship logic. also avoid
        // referencing an undefined $product variable.
        $product = Product::create($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->update(['image' => $path]);
        }

        if (isset($validated['categories'])) {
            $product->categories()->attach($validated['categories']);
        }

        return response()->json($product->load('categories'), 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product->load('categories'));
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        $product->update($validated);

        if (isset($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        return response()->json($product);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted'
        ]);
        //
    }
}
