<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:6|max:20',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:20',
        ]);

        $table = Table::findOrFail($data['table_id']);

        if ((int) $data['guests'] > (int) $table->seats) {
            return response()->json([
                'message' => 'Kjo tavolinë nuk ka aq vende.'
            ], 422);
        }

        $requestedStart = Carbon::createFromFormat('H:i', $data['time']);
        $requestedEnd = $requestedStart->copy()->addHours(2);

        $exists = Reservation::query()
            ->where('table_id', $table->id)
            ->where('date', $data['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->contains(function ($reservation) use ($requestedStart, $requestedEnd) {
                $reservationStart = Carbon::createFromFormat('H:i:s', $reservation->time);
                $reservationEnd = Carbon::createFromFormat(
                    'H:i:s',
                    $reservation->actual_end_time ?: $reservation->end_time
                );

                return $requestedStart < $reservationEnd && $requestedEnd > $reservationStart;
            });

        if ($exists) {
            return response()->json([
                'message' => 'Kjo tavolinë është e rezervuar në këtë interval kohe.'
            ], 409);
        }

        $reservation = Reservation::create([
            'user_id' => $user?->id,
            'table_id' => $table->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'],
            'time' => $requestedStart->format('H:i:s'),
            'end_time' => $requestedEnd->format('H:i:s'),
            'actual_end_time' => null,
            'guests' => (int) $data['guests'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservation created',
            'data' => $reservation->load(['table', 'user'])
        ], 201);
    }

    public function myReservations()
{
    $user = auth('api')->user();

    $reservations = Reservation::with(['table'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    return response()->json([
        'message' => 'Your reservations fetched successfully',
        'data' => $reservations
    ]);
}

public function updateMine(Request $request, Reservation $reservation)
{
    $user = auth('api')->user();

    if ($reservation->user_id !== $user->id) {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
    }

    if ($reservation->status === 'cancelled') {
        return response()->json([
            'message' => 'Cancelled reservation cannot be edited.'
        ], 422);
    }

    $reservationDateTime = \Carbon\Carbon::parse($reservation->date . ' ' . $reservation->time);
    if ($reservationDateTime->isPast()) {
        return response()->json([
            'message' => 'Past reservations cannot be edited.'
        ], 422);
    }

    $data = $request->validate([
        'table_id' => 'required|integer|exists:tables,id',
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'guests' => 'required|integer|min:1|max:20',
        'name' => 'required|string|min:2|max:255',
        'phone' => 'required|string|min:6|max:20',
    ]);

    $newDateTime = \Carbon\Carbon::parse($data['date'] . ' ' . $data['time']);
    if ($newDateTime->isPast()) {
        return response()->json([
            'message' => 'You can edit only upcoming reservations.'
        ], 422);
    }

    $table = \App\Models\Table::findOrFail($data['table_id']);

    if ((int) $data['guests'] > (int) $table->seats) {
        return response()->json([
            'message' => 'Selected table does not have enough seats.'
        ], 422);
    }

    $newStart = \Carbon\Carbon::createFromFormat('H:i', $data['time']);
    $newEnd = $newStart->copy()->addHours(2);

    $exists = Reservation::query()
        ->where('id', '!=', $reservation->id)
        ->where('table_id', $data['table_id'])
        ->where('date', $data['date'])
        ->whereIn('status', ['pending', 'confirmed'])
        ->get()
        ->contains(function ($otherReservation) use ($newStart, $newEnd) {
            $otherStart = \Carbon\Carbon::createFromFormat('H:i:s', $otherReservation->time);
            $otherEnd = \Carbon\Carbon::createFromFormat(
                'H:i:s',
                $otherReservation->actual_end_time ?: $otherReservation->end_time
            );

            return $newStart < $otherEnd && $newEnd > $otherStart;
        });

    if ($exists) {
        return response()->json([
            'message' => 'This table is already reserved in that time range.'
        ], 409);
    }

    $reservation->update([
        'table_id' => $data['table_id'],
        'name' => $data['name'],
        'phone' => $data['phone'],
        'date' => $data['date'],
        'time' => $newStart->format('H:i:s'),
        'end_time' => $newEnd->format('H:i:s'),
        'guests' => $data['guests'],
    ]);

    return response()->json([
        'message' => 'Reservation updated successfully',
        'data' => $reservation->fresh()->load('table')
    ]);
}

    public function index(Request $request)
    {
        $status = $request->query('status');
        $date = $request->query('date');

        $q = Reservation::with(['table', 'user'])
            ->when($status && $status !== 'all', fn ($qq) => $qq->where('status', $status))
            ->when($date, fn ($qq) => $qq->where('date', $date))
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($q);
    }

    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['table', 'user']));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'table_id' => 'sometimes|required|integer|exists:tables,id',
            'name' => 'sometimes|required|string|min:2|max:255',
            'phone' => 'sometimes|required|string|min:6|max:20',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|date_format:H:i',
            'guests' => 'sometimes|required|integer|min:1|max:20',
            'status' => 'sometimes|required|in:pending,confirmed,cancelled',
            'actual_end_time' => 'nullable|date_format:H:i',
        ]);

        $newTableId = $data['table_id'] ?? $reservation->table_id;
        $newDate = $data['date'] ?? $reservation->date;
        $newTime = $data['time'] ?? substr($reservation->time, 0, 5);
        $newGuests = (int) ($data['guests'] ?? $reservation->guests);

        $table = Table::findOrFail($newTableId);

        if ($newGuests > (int) $table->seats) {
            return response()->json([
                'message' => 'Guests exceed table seats.'
            ], 422);
        }

        $newStart = Carbon::createFromFormat('H:i', $newTime);
        $newEnd = $newStart->copy()->addHours(2);

        if (array_key_exists('actual_end_time', $data) && !empty($data['actual_end_time'])) {
            $actualEnd = Carbon::createFromFormat('H:i', $data['actual_end_time']);

            if ($actualEnd->lessThanOrEqualTo($newStart)) {
                return response()->json([
                    'message' => 'Actual end time must be later than start time.'
                ], 422);
            }
        }

        $exists = Reservation::query()
            ->where('id', '!=', $reservation->id)
            ->where('table_id', $newTableId)
            ->where('date', $newDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->contains(function ($otherReservation) use ($newStart, $newEnd) {
                $otherStart = Carbon::createFromFormat('H:i:s', $otherReservation->time);
                $otherEnd = Carbon::createFromFormat(
                    'H:i:s',
                    $otherReservation->actual_end_time ?: $otherReservation->end_time
                );

                return $newStart < $otherEnd && $newEnd > $otherStart;
            });

        if ($exists) {
            return response()->json([
                'message' => 'Nuk mund të update: conflict me rezervim tjetër.'
            ], 409);
        }

        $updateData = [];

        if (array_key_exists('table_id', $data)) $updateData['table_id'] = $data['table_id'];
        if (array_key_exists('name', $data)) $updateData['name'] = $data['name'];
        if (array_key_exists('phone', $data)) $updateData['phone'] = $data['phone'];
        if (array_key_exists('date', $data)) $updateData['date'] = $data['date'];
        if (array_key_exists('time', $data)) {
            $updateData['time'] = $newStart->format('H:i:s');
            $updateData['end_time'] = $newEnd->format('H:i:s');
        }
        if (array_key_exists('guests', $data)) $updateData['guests'] = $data['guests'];
        if (array_key_exists('status', $data)) $updateData['status'] = $data['status'];
        if (array_key_exists('actual_end_time', $data)) {
            $updateData['actual_end_time'] = $data['actual_end_time']
                ? Carbon::createFromFormat('H:i', $data['actual_end_time'])->format('H:i:s')
                : null;
        }

        $reservation->update($updateData);

        return response()->json([
            'message' => 'Reservation updated',
            'data' => $reservation->fresh()->load(['table', 'user'])
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted'
        ]);
    }
}