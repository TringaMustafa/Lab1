<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TableController extends Controller
{
    private function normalizeTime($time)
    {
        if (!$time) {
            return null;
        }

        try {
            $time = trim((string) $time);
            $time = preg_replace('/\s+/', ' ', $time);

            $formats = [
                'h:i A',
                'g:i A',
                'h:iA',
                'g:iA',
                'H:i',
                'G:i',
                'H:i:s',
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, strtoupper($time))->format('H:i');
                } catch (\Exception $e) {
                }
            }

            return Carbon::parse($time)->format('H:i');
        } catch (\Exception $e) {
            return null;
        }
    }

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
        $guests = (int) $data['guests'];

        $normalizedTime = $this->normalizeTime($data['time']);

        if (!$normalizedTime) {
            return response()->json([
                'message' => 'Invalid time format.'
            ], 422);
        }

        $requestedTime = Carbon::createFromFormat('H:i', $normalizedTime);

        $tables = Table::query()
            ->where('seats', '>=', $guests)
            ->get()
            ->filter(function ($table) use ($date, $requestedTime) {
                foreach ($table->reservations()
                    ->where('date', $date)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->get() as $reservation) {

                    $reservationTime = Carbon::createFromFormat('H:i:s', $reservation->time);

                    $blockedStart = $reservationTime->copy()->subMinutes(90);
                    $blockedEnd = $reservationTime->copy()->addMinutes(90);

                    if ($requestedTime->greaterThan($blockedStart) && $requestedTime->lessThan($blockedEnd)) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

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