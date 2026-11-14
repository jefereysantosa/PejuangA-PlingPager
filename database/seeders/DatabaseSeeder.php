<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a dummy Merchant
        $user = User::create([
            'name' => 'Admin Pejuang',
            'email' => 'admin@pejuang.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create the Store Profile
        StoreProfile::create([
            'user_id' => $user->id,
            'store_name' => 'Kedai Pejuang',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_owner_name' => 'Admin Pejuang',
        ]);

        // 3. Create Dummy Products (Menu)
        $meal1 = Product::create(['user_id' => $user->id, 'name' => 'Nasi Goreng Pejuang', 'category' => 'meal', 'price' => 25000]);
        $meal2 = Product::create(['user_id' => $user->id, 'name' => 'Ayam Geprek', 'category' => 'meal', 'price' => 20000]);
        $bev1 = Product::create(['user_id' => $user->id, 'name' => 'Es Teh Manis', 'category' => 'beverage', 'price' => 5000]);
        $bev2 = Product::create(['user_id' => $user->id, 'name' => 'Kopi Susu', 'category' => 'beverage', 'price' => 15000]);

        // 4. Create Dummy Orders & Items (To test UI immediately)

        // Order 1: Processing
        $order1 = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD-0001',
            'customer_name' => 'Budi',
            'total_price' => 30000,
            'status' => 'processing',
            'notes' => 'Tidak pedas',
        ]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $meal1->id, 'quantity' => 1, 'price_at_time' => 25000]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $bev1->id, 'quantity' => 1, 'price_at_time' => 5000]);

        // Order 2: Ready
        $order2 = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD-0002',
            'customer_name' => 'Siti',
            'total_price' => 35000,
            'status' => 'ready',
            'notes' => 'Es dipisah',
        ]);
        OrderItem::create(['order_id' => $order2->id, 'product_id' => $meal2->id, 'quantity' => 1, 'price_at_time' => 20000]);
        OrderItem::create(['order_id' => $order2->id, 'product_id' => $bev2->id, 'quantity' => 1, 'price_at_time' => 15000]);

        // Order 3: Processing (Just drinks)
        $order3 = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD-0003',
            'customer_name' => 'Andi',
            'total_price' => 10000,
            'status' => 'processing',
        ]);
        OrderItem::create(['order_id' => $order3->id, 'product_id' => $bev1->id, 'quantity' => 2, 'price_at_time' => 5000]);
    }
}
