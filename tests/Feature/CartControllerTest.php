<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_view_cart()
    {
        $user = User::create([
            'name' => 'Cart Viewer',
            'email' => 'cartviewer@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/carts');

        $response->assertStatus(200)
                 ->assertJson(['id' => $cart->id]);
    }

    /** @test */
    public function can_add_product_to_cart()
    {
        $user = User::create([
            'name' => 'Cart Adder',
            'email' => 'cartadder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'A test product',
            'price' => 99.99,
            'stock' => 100
        ]);

        $response = $this->actingAs($user)->postJson('/api/carts/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Added to cart']);

        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function adding_same_product_increments_quantity()
    {
        $user = User::create([
            'name' => 'Increment User',
            'email' => 'increment@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $product = Product::create([
            'name' => 'Incremental Product',
            'description' => 'Product to increment',
            'price' => 50.00,
            'stock' => 200
        ]);

        $this->actingAs($user)->postJson('/api/carts/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->actingAs($user)->postJson('/api/carts/add', [
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $cartItem = CartItem::where('product_id', $product->id)->first();
        $this->assertEquals(5, $cartItem->quantity);
    }

    /** @test */
    public function add_requires_valid_product_id()
    {
        $user = User::create([
            'name' => 'Validator User',
            'email' => 'validator@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $response = $this->actingAs($user)->postJson('/api/carts/add', [
            'product_id' => 9999,
            'quantity' => 1
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function add_requires_quantity()
    {
        $user = User::create([
            'name' => 'Quantity Validator',
            'email' => 'qtyvalidator@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $product = Product::create([
            'name' => 'Qty Test Product',
            'price' => 75.00,
            'stock' => 50
        ]);

        $response = $this->actingAs($user)->postJson('/api/carts/add', [
            'product_id' => $product->id
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function can_remove_item_from_cart()
    {
        $user = User::create([
            'name' => 'Cart Remover',
            'email' => 'cartremover@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Remove Product',
            'description' => 'To be removed',
            'price' => 125.00,
            'stock' => 30
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/carts/items/{$item->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item removed']);

        $this->assertNull(CartItem::find($item->id));
    }

    /** @test */
    public function cart_shows_items_with_products()
    {
        $user = User::create([
            'name' => 'Cart Detail Viewer',
            'email' => 'cartdetail@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = Product::create([
            'name' => 'Product A',
            'price' => 100.00,
            'stock' => 50
        ]);

        $product2 = Product::create([
            'name' => 'Product B',
            'price' => 200.00,
            'stock' => 25
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 1
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user)->getJson('/api/carts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'items' => [
                         '*' => ['id', 'product_id', 'quantity', 'product']
                     ]
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_add_to_cart()
    {
        $product = Product::create([
            'name' => 'Auth Test Product',
            'price' => 50.00,
            'stock' => 100
        ]);

        $response = $this->postJson('/api/carts/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_cart()
    {
        $response = $this->getJson('/api/carts');

        $response->assertStatus(401);
    }
}
