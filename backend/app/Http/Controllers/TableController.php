<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // USER - list tables
  public function index()
{
    return response()->json([
        'message' => 'Tables fetched successfully',
        'data' => Table::orderBy('id')->get(),
    ]);
}
    // USER - available tables
    public function available(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'guests' => 'required|integer|min:1',
        ]);

        $date = $data['date'];
        $time = $data['time'];
        $guests = (int) $data['guests'];

        $tables = Table::query()
            ->where('seats', '>=', $guests)
            ->whereDoesntHave('reservations', function ($q) use ($date, $time) {
                $q->where('date', $date)
                  ->whereTime('time', $time)
                  ->whereIn('status', ['pending', 'confirmed']);
            })
            ->orderBy('id')
            ->get();

      return response()->json([
    'message' => 'Available tables fetched successfully',
    'data' => $tables
]);
    }

    // ADMIN - create table
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:1',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:free,occupied,reserved,cleaning',
        ]);

        $table = Table::create($data);

        return response()->json([
            'message' => 'Table created successfully',
            'data' => $table
        ], 201);
    }

    // ADMIN - show one table
    public function show(Table $table)
    {
        return response()->json($table);
    }

    // ADMIN - update table
    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|min:1',
            'seats' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:free,occupied,reserved,cleaning',
        ]);

        $table->update($data);

        return response()->json([
            'message' => 'Table updated successfully',
            'data' => $table
        ]);
    }

    // ADMIN - delete table
    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json([
            'message' => 'Table deleted successfully'
        ]);
    }
}