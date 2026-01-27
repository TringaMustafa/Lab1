<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,visa,paypal',
            'phone' => 'required|string',
        ]);

        $user = auth('api')->user();

        // ✅ kalkulo totalin prej items
        $total = collect($request->items)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        // ✅ krijo order
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
        ]);

        // ✅ ruaj order items
        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['qty'],
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id
        ], 201);
    }
}
