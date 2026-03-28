<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Table;
use App\Models\Order;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'totalOrders' => Order::count(),
            'todayReservations' => Reservation::whereDate('created_at', now()->toDateString())->count(),
            'totalTables' => Table::count(),
            'totalMenus' => Menu::count(),
        ]);
    }
}