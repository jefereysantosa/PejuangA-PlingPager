<?php

namespace Tests\Feature\Cashier;

use App\Livewire\Cashier\OrderTaker;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTakerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_submit_an_order_with_database_fields(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Kopi Susu',
            'category' => 'beverage',
            'price' => 18000,
            'is_available' => true,
        ]);

        Livewire::actingAs($user)
            ->test(OrderTaker::class)
            ->set('consumerName', 'Budi')
            ->call('addToCart', $product->id)
            ->call('submitOrder')
            ->assertHasNoErrors();

        $order = Order::query()->sole();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'customer_name' => 'Budi',
            'total_price' => 18000,
            'status' => 'processing',
        ]);
        $this->assertStringStartsWith('ORD-', $order->order_code);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_time' => 18000,
        ]);
    }
}
