<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function order_item_can_be_created()
    {
        $user = User::create([
            'name' => 'Order Item User',
            'email' => 'orderitemuser@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 100.00,
            'status' => 'pending'
        ]);

        $product = Product::create([
            'name' => 'Item Product',
            'price' => 50.00,
            'stock' => 100
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00
        ]);

        $this->assertInstanceOf(OrderItem::class, $orderItem);
    }

    /** @test */
    public function order_item_belongs_to_order()
    {
        $user = User::create([
            'name' => 'Order Ref User',
            'email' => 'orderref@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 150.00,
            'status' => 'completed'
        ]);

        $product = Product::create([
            'name' => 'Order Ref Product',
            'price' => 75.00,
            'stock' => 50
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 75.00
        ]);

        $this->assertInstanceOf(Order::class, $orderItem->order);
        $this->assertEquals($order->id, $orderItem->order->id);
    }

    /** @test */
    public function order_item_belongs_to_product()
    {
        $user = User::create([
            'name' => 'Product Ref User',
            'email' => 'prodreforder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 200.00,
            'status' => 'pending'
        ]);

        $product = Product::create([
            'name' => 'Referenced Product',
            'price' => 100.00,
            'stock' => 25
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        $this->assertInstanceOf(Product::class, $orderItem->product);
        $this->assertEquals($product->id, $orderItem->product->id);
    }

    /** @test */
    public function order_item_has_required_attributes()
    {
        $user = User::create([
            'name' => 'Attr Order Item User',
            'email' => 'attrorderitem@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 250.00,
            'status' => 'shipped'
        ]);

        $product = Product::create([
            'name' => 'Attr Product',
            'price' => 125.00,
            'stock' => 75
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 125.00
        ]);

        $this->assertEquals($order->id, $orderItem->order_id);
        $this->assertEquals($product->id, $orderItem->product_id);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(125.00, $orderItem->price);
    }

    /** @test */
    public function order_can_have_multiple_items()
    {
        $user = User::create([
            'name' => 'Multi Item User',
            'email' => 'multiitem@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 300.00,
            'status' => 'pending'
        ]);

        $product1 = Product::create([
            'name' => 'Item 1',
            'price' => 100.00,
            'stock' => 50
        ]);

        $product2 = Product::create([
            'name' => 'Item 2',
            'price' => 150.00,
            'stock' => 30
        ]);

        $product3 = Product::create([
            'name' => 'Item 3',
            'price' => 50.00,
            'stock' => 100
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 100.00
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 150.00
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product3->id,
            'quantity' => 1,
            'price' => 50.00
        ]);

        $this->assertCount(3, $order->items);
    }

    /** @test */
    public function order_item_can_be_deleted()
    {
        $user = User::create([
            'name' => 'Delete Item User',
            'email' => 'deleteitem@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 175.00,
            'status' => 'pending'
        ]);

        $product = Product::create([
            'name' => 'Delete Product',
            'price' => 175.00,
            'stock' => 40
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 175.00
        ]);

        $orderItemId = $orderItem->id;
        $orderItem->delete();

        $this->assertNull(OrderItem::find($orderItemId));
    }

    /** @test */
    public function order_item_fillable_attributes_are_correct()
    {
        $fillable = OrderItem::make()->getFillable();

        $this->assertContains('order_id', $fillable);
        $this->assertContains('product_id', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('price', $fillable);
    }

    /** @test */
    public function order_item_price_is_captured_at_time_of_order()
    {
        $user = User::create([
            'name' => 'Price Capture User',
            'email' => 'pricecapture@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 99.99,
            'status' => 'pending'
        ]);

        $product = Product::create([
            'name' => 'Price Product',
            'price' => 99.99,
            'stock' => 20
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 99.99
        ]);

        // Simulate product price change
        $product->update(['price' => 79.99]);

        // Order item should still have original price
        $this->assertEquals(99.99, $orderItem->price);
        $this->assertEquals(79.99, $product->fresh()->price);
    }
}
