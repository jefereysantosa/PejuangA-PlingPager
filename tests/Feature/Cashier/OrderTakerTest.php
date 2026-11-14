<?php

namespace Tests\Feature\Cashier;

use App\Livewire\Cashier\OrderTaker;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function test_cashier_only_sees_their_own_available_products(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $productA = Product::create([
            'user_id' => $userA->id,
            'name' => 'Menu Toko A',
            'category' => 'meal',
            'price' => 20000,
            'is_available' => true,
        ]);

        $unavailableProductA = Product::create([
            'user_id' => $userA->id,
            'name' => 'Menu Toko A Habis',
            'category' => 'meal',
            'price' => 25000,
            'is_available' => false,
        ]);

        $productB = Product::create([
            'user_id' => $userB->id,
            'name' => 'Menu Toko B',
            'category' => 'meal',
            'price' => 30000,
            'is_available' => true,
        ]);

        Livewire::actingAs($userA)
            ->test(OrderTaker::class)
            ->assertSee($productA->name)
            ->assertDontSee($unavailableProductA->name)
            ->assertDontSee($productB->name);
    }

    public function test_cashier_cannot_add_another_users_product_to_cart(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $productB = Product::create([
            'user_id' => $userB->id,
            'name' => 'Menu Toko B',
            'category' => 'meal',
            'price' => 30000,
            'is_available' => true,
        ]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($userA)
            ->test(OrderTaker::class)
            ->call('addToCart', $productB->id);
    }
}
