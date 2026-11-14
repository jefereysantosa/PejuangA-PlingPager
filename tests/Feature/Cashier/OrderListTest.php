<?php

namespace Tests\Feature\Cashier;

use App\Livewire\Cashier\OrderEditor;
use App\Livewire\Cashier\OrderList;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderListTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_only_sees_their_own_orders(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderA = Order::create([
            'user_id' => $userA->id,
            'order_code' => 'ORD-AAA111',
            'customer_name' => 'Konsumen Toko A',
            'total_price' => 20000,
            'status' => 'processing',
        ]);

        $orderB = Order::create([
            'user_id' => $userB->id,
            'order_code' => 'ORD-BBB222',
            'customer_name' => 'Konsumen Toko B',
            'total_price' => 30000,
            'status' => 'processing',
        ]);

        Livewire::actingAs($userA)
            ->test(OrderList::class)
            ->assertSee($orderA->customer_name)
            ->assertSee($orderA->order_code)
            ->assertDontSee($orderB->customer_name)
            ->assertDontSee($orderB->order_code);
    }

    public function test_cashier_cannot_mark_another_users_order_as_ready(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderB = Order::create([
            'user_id' => $userB->id,
            'order_code' => 'ORD-BBB222',
            'customer_name' => 'Konsumen Toko B',
            'total_price' => 30000,
            'status' => 'processing',
        ]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($userA)
            ->test(OrderList::class)
            ->call('markAsReady', $orderB->id);
    }

    public function test_cashier_cannot_load_or_edit_another_users_order(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderB = Order::create([
            'user_id' => $userB->id,
            'order_code' => 'ORD-BBB222',
            'customer_name' => 'Konsumen Toko B',
            'total_price' => 30000,
            'status' => 'processing',
        ]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($userA)
            ->test(OrderEditor::class)
            ->call('loadOrder', $orderB->id);
    }
}
