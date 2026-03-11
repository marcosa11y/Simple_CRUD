<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // ensure a cart record always exists for the user; makes it easier for
        // clients and normalizes responses for the feature tests.
        $cart = Cart::with('items.product')
            ->firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id
        ]);

        $item = $cart->items()->where('product_id', $validated['product_id'])->first();

        if ($item) {
            $item->increment('quantity', $validated['quantity']);
        } else {
            $cart->items()->create($validated);
        }

        return response()->json(['message' => 'Added to cart']);
    }

    public function remove(CartItem $item)
    {
        $item->delete();

        return response()->json(['message' => 'Item removed']);
    }
}