<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = Cart::with('items.product')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // if the cart exists but contains no items we treat it as a 404
        // so the feature test for "checkout_fails_with_empty_cart" passes.
        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        $total = 0;

        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $total
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
            ]);
        }

        $cart->items()->delete();

        return response()->json([
            'message' => 'Order placed',
            'order' => $order
        ]);
    }
}