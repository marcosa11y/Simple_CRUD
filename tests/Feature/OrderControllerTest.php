<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_checkout_from_cart()
    {
        $user = User::create([
            'name' => 'Checkout User',
            'email' => 'checkout@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = Product::create([
            'name' => 'Checkout Product 1',
            'price' => 50.00,
            'stock' => 100
        ]);

        $product2 = Product::create([
            'name' => 'Checkout Product 2',
            'price' => 75.00,
            'stock' => 50
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders/checkout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Order placed']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 175.00
        ]);
    }

    /** @test */
    public function checkout_calculates_total_correctly()
    {
        $user = User::create([
            'name' => 'Total Calculator',
            'email' => 'calculator@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Total Product',
            'price' => 99.99,
            'stock' => 10
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders/checkout');

        $response->assertStatus(200);

        $expectedTotal = 99.99 * 3;
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => $expectedTotal
        ]);
    }

    /** @test */
    public function checkout_creates_order_items()
    {
        $user = User::create([
            'name' => 'Order Item Creator',
            'email' => 'orderitem@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = Product::create([
            'name' => 'Order Item 1',
            'price' => 25.00,
            'stock' => 200
        ]);

        $product2 = Product::create([
            'name' => 'Order Item 2',
            'price' => 35.00,
            'stock' => 150
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders/checkout');

        $response->assertStatus(200);

        $order = Order::where('user_id', $user->id)->first();

        $this->assertCount(2, $order->items);
    }

    /** @test */
    public function checkout_clears_cart()
    {
        $user = User::create([
            'name' => 'Cart Clearer',
            'email' => 'clearer@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Clear Product',
            'price' => 50.00,
            'stock' => 100
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->actingAs($user)->postJson('/api/orders/checkout');

        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
    }

    /** @test */
    public function checkout_fails_with_empty_cart()
    {
        $user = User::create([
            'name' => 'Empty Cart User',
            'email' => 'emptycart@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders/checkout');

        $response->assertStatus(404);
    }

    /** @test */
    public function checkout_creates_order_with_pending_status()
    {
        $user = User::create([
            'name' => 'Pending Status User',
            'email' => 'pending@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Pending Product',
            'price' => 100.00,
            'stock' => 50
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->actingAs($user)->postJson('/api/orders/checkout');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_checkout()
    {
        $response = $this->postJson('/api/orders/checkout');

        $response->assertStatus(401);
    }
}
