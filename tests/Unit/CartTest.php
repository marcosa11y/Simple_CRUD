<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cart_can_be_created()
    {
        $user = User::create([
            'name' => 'Cart User',
            'email' => 'cart@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(Cart::class, $cart);
    }

    /** @test */
    public function cart_belongs_to_user()
    {
        $user = User::create([
            'name' => 'Cart Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $cart->user);
        $this->assertEquals($user->id, $cart->user->id);
    }

    /** @test */
    public function cart_can_have_many_items()
    {
        $user = User::create([
            'name' => 'Multi Cart User',
            'email' => 'multi@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product1 = Product::create([
            'name' => 'Item 1',
            'price' => 10.00,
            'stock' => 100
        ]);

        $product2 = Product::create([
            'name' => 'Item 2',
            'price' => 20.00,
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

        $this->assertCount(2, $cart->items);
    }

    /** @test */
    public function cart_items_relationship_works()
    {
        $user = User::create([
            'name' => 'Item Test User',
            'email' => 'items@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->assertTrue($cart->items->contains($item));
    }

    /** @test */
    public function cart_can_be_deleted()
    {
        $user = User::create([
            'name' => 'Delete Cart User',
            'email' => 'deletecart@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cartId = $cart->id;

        $cart->delete();

        $this->assertNull(Cart::find($cartId));
    }

    /** @test */
    public function cart_fillable_attributes_are_correct()
    {
        $fillable = Cart::make()->getFillable();

        $this->assertContains('user_id', $fillable);
    }
}
