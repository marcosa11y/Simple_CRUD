<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function order_can_be_created()
    {
        $user = User::create([
            'name' => 'Order User',
            'email' => 'order@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 99.99,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 99.99,
            'status' => 'pending'
        ]);

        $this->assertInstanceOf(Order::class, $order);
    }

    /** @test */
    public function order_has_default_status()
    {
        $user = User::create([
            'name' => 'Status User',
            'email' => 'status@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 149.99
        ]);

        $this->assertEquals('pending', $order->status);
    }

    /** @test */
    public function order_has_required_attributes()
    {
        $user = User::create([
            'name' => 'Attr Order User',
            'email' => 'attrorder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 249.99,
            'status' => 'completed'
        ]);

        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(249.99, $order->total);
        $this->assertEquals('completed', $order->status);
    }

    /** @test */
    public function order_can_be_updated()
    {
        $user = User::create([
            'name' => 'Update Order User',
            'email' => 'updateorder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 199.99,
            'status' => 'pending'
        ]);

        $order->update(['status' => 'shipped']);

        $this->assertEquals('shipped', $order->fresh()->status);
    }

    /** @test */
    public function order_can_be_deleted()
    {
        $user = User::create([
            'name' => 'Delete Order User',
            'email' => 'deleteorder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 299.99,
            'status' => 'pending'
        ]);

        $orderId = $order->id;
        $order->delete();

        $this->assertNull(Order::find($orderId));
    }

    /** @test */
    public function multiple_orders_can_belong_to_same_user()
    {
        $user = User::create([
            'name' => 'Multi Order User',
            'email' => 'multiorder@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        Order::create([
            'user_id' => $user->id,
            'total' => 50.00,
            'status' => 'pending'
        ]);

        Order::create([
            'user_id' => $user->id,
            'total' => 75.00,
            'status' => 'completed'
        ]);

        Order::create([
            'user_id' => $user->id,
            'total' => 100.00,
            'status' => 'shipped'
        ]);

        $userOrders = Order::where('user_id', $user->id)->get();

        $this->assertCount(3, $userOrders);
    }

    /** @test */
    public function order_total_is_calculated_correctly()
    {
        $user = User::create([
            'name' => 'Calc User',
            'email' => 'calc@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $total = 349.50;

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'confirmed'
        ]);

        $this->assertEquals($total, $order->total);
    }

    /** @test */
    public function order_has_timestamps()
    {
        $user = User::create([
            'name' => 'Timestamp User',
            'email' => 'timestamp@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 159.99,
            'status' => 'pending'
        ]);

        $this->assertNotNull($order->created_at);
        $this->assertNotNull($order->updated_at);
    }
}
