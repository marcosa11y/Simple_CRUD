<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cart_item_can_be_created()
    {
        $user = User::create([
            'name' => 'Item User',
            'email' => 'itemuser@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Item Product',
            'price' => 50.00,
            'stock' => 100
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertInstanceOf(CartItem::class, $cartItem);
    }

    /** @test */
    public function cart_item_belongs_to_product()
    {
        $user = User::create([
            'name' => 'Product Ref User',
            'email' => 'prodref@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Referenced Product',
            'price' => 75.00,
            'stock' => 50
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->assertInstanceOf(Product::class, $cartItem->product);
        $this->assertEquals($product->id, $cartItem->product->id);
    }

    /** @test */
    public function cart_item_has_required_attributes()
    {
        $user = User::create([
            'name' => 'Attr User',
            'email' => 'attr@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Attr Product',
            'price' => 100.00,
            'stock' => 200
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $this->assertEquals($cart->id, $cartItem->cart_id);
        $this->assertEquals($product->id, $cartItem->product_id);
        $this->assertEquals(5, $cartItem->quantity);
    }

    /** @test */
    public function cart_item_quantity_can_be_incremented()
    {
        $user = User::create([
            'name' => 'Increment User',
            'email' => 'increment@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Increment Product',
            'price' => 30.00,
            'stock' => 150
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $cartItem->increment('quantity', 3);

        $this->assertEquals(5, $cartItem->fresh()->quantity);
    }

    /** @test */
    public function cart_item_can_be_deleted()
    {
        $user = User::create([
            'name' => 'Delete Item User',
            'email' => 'deleteitem@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $cart = Cart::create(['user_id' => $user->id]);

        $product = Product::create([
            'name' => 'Delete Product',
            'price' => 45.00,
            'stock' => 80
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $cartItemId = $cartItem->id;
        $cartItem->delete();

        $this->assertNull(CartItem::find($cartItemId));
    }

    /** @test */
    public function cart_item_fillable_attributes_are_correct()
    {
        $fillable = CartItem::make()->getFillable();

        $this->assertContains('cart_id', $fillable);
        $this->assertContains('product_id', $fillable);
        $this->assertContains('quantity', $fillable);
    }
}
