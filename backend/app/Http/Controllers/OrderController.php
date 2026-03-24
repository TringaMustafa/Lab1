<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // USER - create order
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,visa,paypal',
            'phone' => 'required|string|min:6',
        ]);

        $user = auth('api')->user();

        $total = collect($request->items)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'price' => $item['price'],
                'qty' => $item['qty'],
                'line_total' => $item['price'] * $item['qty'],
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id
        ], 201);
    }

    // ADMIN - list orders
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['user', 'items.menu'])
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($orders);
    }

    // ADMIN - show single order
    public function show(Order $order)
    {
        return response()->json($order->load(['user', 'items.menu']));
    }

    // ADMIN - update order
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'sometimes|required|in:pending,confirmed,completed,cancelled',
            'payment_status' => 'sometimes|required|in:pending,paid,failed',
            'phone' => 'sometimes|required|string|min:6',
        ]);

        $order->update($data);

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order->fresh()->load(['user', 'items.menu'])
        ]);
    }

    // ADMIN - delete order
    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}