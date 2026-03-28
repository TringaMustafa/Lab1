<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function isValidPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) < 8 || strlen($phone) > 15) {
            return false;
        }

        // bllokon 111111111, 222222222, 000000000 etj.
        if (preg_match('/^(\d)\1+$/', $phone)) {
            return false;
        }

        $fakeNumbers = [
            '12345678',
            '123456789',
            '1234567890',
            '12345678901',
            '00000000',
            '000000000',
            '0000000000',
            '11111111',
            '111111111',
            '1111111111',
            '22222222',
            '222222222',
            '2222222222',
            '33333333',
            '333333333',
            '44444444',
            '444444444',
            '55555555',
            '555555555',
            '66666666',
            '666666666',
            '77777777',
            '777777777',
            '88888888',
            '888888888',
            '99999999',
            '999999999',
            '9999999999',
        ];

        if (in_array($phone, $fakeNumbers)) {
            return false;
        }

        return true;
    }

    // USER - create order
    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,visa,paypal',
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
        ]);

        if (!$this->isValidPhone($data['phone'])) {
            return response()->json([
                'message' => 'Please enter a valid phone number.'
            ], 422);
        }

        $user = auth('api')->user();

        $total = collect($data['items'])->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'phone' => $data['phone'],
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
        ]);

        foreach ($data['items'] as $item) {
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

    public function myOrders()
    {
        $user = auth('api')->user();

        $orders = \App\Models\Order::with(['items.menu'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Your orders fetched successfully',
            'data' => $orders
        ]);
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
            'phone' => ['sometimes', 'required', 'string', 'min:8', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
        ]);

        if (array_key_exists('phone', $data) && !$this->isValidPhone($data['phone'])) {
            return response()->json([
                'message' => 'Please enter a valid phone number.'
            ], 422);
        }

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