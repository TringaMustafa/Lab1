<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * ✅ USER - Create reservation
     * POST /api/reservations
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'name' => 'required|string|min:2',
            'phone' => 'required|string|min:6',
            'date' => 'required|date',
            'time' => 'required',
            'guests' => 'required|integer|min:1',
        ]);

        $table = Table::findOrFail($data['table_id']);

        // ✅ mos lejo guests > seats
        if ((int)$data['guests'] > (int)$table->seats) {
            return response()->json([
                'message' => 'Kjo tavolinë nuk ka aq vende.'
            ], 422);
        }

        // ✅ conflict check (same table + same date + same time)
        $exists = Reservation::query()
            ->where('table_id', $table->id)
            ->where('date', $data['date'])
            ->whereTime('time', $data['time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Kjo tavolinë është e rezervuar për këtë orë.'
            ], 409);
        }

        $reservation = Reservation::create([
            'user_id' => $user?->id,
            'table_id' => $table->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'],
            'time' => $data['time'],
            'guests' => (int)$data['guests'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservation created',
            'data' => $reservation
        ], 201);
    }

    /**
     * ✅ ADMIN - List reservations
     * GET /api/reservations
     */
    public function index(Request $request)
    {
        $status = $request->query('status'); // pending/confirmed/cancelled
        $date = $request->query('date');     // YYYY-MM-DD

        $q = Reservation::with(['table', 'user'])
            ->when($status && $status !== 'all', fn ($qq) => $qq->where('status', $status))
            ->when($date, fn ($qq) => $qq->where('date', $date))
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($q);
    }

    /**
     * ✅ ADMIN - Show reservation
     * GET /api/reservations/{reservation}
     */
    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['table', 'user']));
    }

    /**
     * ✅ ADMIN - Update reservation (confirm/cancel/change)
     * PUT/PATCH /api/reservations/{reservation}
     */
    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'table_id' => 'sometimes|required|integer|exists:tables,id',
            'name' => 'sometimes|required|string|min:2',
            'phone' => 'sometimes|required|string|min:6',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required',
            'guests' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:pending,confirmed,cancelled',
        ]);

        // ✅ nëse po ndryshon table/date/time, kontrollo conflict
        $newTableId = $data['table_id'] ?? $reservation->table_id;
        $newDate = $data['date'] ?? $reservation->date;
        $newTime = $data['time'] ?? $reservation->time;

        $exists = Reservation::query()
            ->where('id', '!=', $reservation->id)
            ->where('table_id', $newTableId)
            ->where('date', $newDate)
            ->whereTime('time', $newTime)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Nuk mund të update: conflict me rezervim tjetër.'
            ], 409);
        }

        // ✅ guests <= seats
        $table = Table::findOrFail($newTableId);
        $newGuests = (int)($data['guests'] ?? $reservation->guests);
        if ($newGuests > (int)$table->seats) {
            return response()->json(['message' => 'Guests exceed table seats.'], 422);
        }

        $reservation->update($data);

        return response()->json([
            'message' => 'Reservation updated',
            'data' => $reservation->fresh()->load(['table', 'user'])
        ]);
    }

    /**
     * ✅ ADMIN - Delete reservation
     * DELETE /api/reservations/{reservation}
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted'
        ]);
    }
}
