<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tool;
use App\Services\AuditLogger;
use App\Services\ToolStatusTransitioner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * FR-15.2 — Booking API endpoints.
 */
class BookingController extends Controller
{
    /**
     * GET /api/v1/bookings — The authenticated user's bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with('tool.depot')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($bookings);
    }

    /**
     * POST /api/v1/bookings — Create a booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tool_id'    => 'required|exists:tools,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $tool = Tool::findOrFail($validated['tool_id']);

        if (! $tool->isAvailable()) {
            throw ValidationException::withMessages([
                'tool_id' => [__('This tool is not currently available.')],
            ]);
        }

        // FR-3.8 — overlap check
        if (Booking::hasOverlap($tool->id, $validated['start_date'], $validated['end_date'])) {
            throw ValidationException::withMessages([
                'start_date' => [__('These dates overlap with an existing booking.')],
            ]);
        }

        $booking = DB::transaction(function () use ($validated, $tool) {
            return Booking::create([
                'tool_id'        => $tool->id,
                'user_id'        => Auth::id(),
                'start_date'     => $validated['start_date'],
                'end_date'       => $validated['end_date'],
                'booking_status' => 'pending',
            ]);
        });

        app(AuditLogger::class)->log('booking.created', $booking);

        return response()->json(['data' => $booking->load('tool')], 201);
    }

    /**
     * PATCH /api/v1/bookings/{booking} — Advance a booking's status.
     */
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:confirm,dispatch,return,cancel',
        ]);

        $transitioner = app(ToolStatusTransitioner::class);

        try {
            match ($validated['action']) {
                'confirm'  => $transitioner->confirm($booking),
                'dispatch' => $transitioner->dispatch($booking),
                'return'   => $transitioner->return($booking),
                'cancel'   => $transitioner->cancel($booking),
            };
        } catch (\LogicException $e) {
            throw ValidationException::withMessages([
                'action' => [$e->getMessage()],
            ]);
        }

        return response()->json(['data' => $booking->fresh()->load('tool')]);
    }
}
