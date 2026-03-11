<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return all categories along with their related products. even if
        // there are none this will simply return an empty collection which
        // makes the JSON count assertions in the feature tests pass.
        return response()->json(Category::with('products')->get());
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
        // validate input, return 422 automatically on failure
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            $category = Category::create($validated);
        } catch (\Exception $e) {
            // log and return a generic 500 message so the client sees the error
            // without the full stack trace; the log will contain the details.
            logger()->error('category creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // load products when returning a single category so that tests which
        // inspect the "products" key succeed.
        return response()->json($category->load('products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category->update($validated);

        return response()->json($category);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
        //
    }
}
