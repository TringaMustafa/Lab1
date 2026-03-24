<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // ✅ USER - list tables
    public function index()
    {
        return response()->json(Table::orderBy('id')->get());
    }

    // ✅ USER - available tables
    public function available(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'time' => 'required',               // "19:30"
            'guests' => 'required|integer|min:1',
        ]);

        $date = $data['date'];
        $time = $data['time'];
        $guests = (int) $data['guests'];

        $tables = Table::query()
            ->where('seats', '>=', $guests)
            ->whereDoesntHave('reservations', function ($q) use ($date, $time) {
                $q->where('date', $date)
                  ->whereTime('time', $time) // ✅ e zgjidh "19:30" vs "19:30:00"
                  ->whereIn('status', ['pending', 'confirmed']);
            })
            ->orderBy('id')
            ->get();

        return response()->json($tables);
    }

    // ✅ ADMIN CRUD methods (store/show/update/destroy) – mbaji si i ke

    
}
