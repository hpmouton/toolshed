<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ToolStatus;
use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\JsonResponse;

/**
 * FR-15.2 — Depot API endpoints.
 */
class DepotController extends Controller
{
    /**
     * GET /api/v1/depots — All active depots.
     */
    public function index(): JsonResponse
    {
        $depots = Depot::where('is_active', true)->orderBy('name')->get();

        return response()->json(['data' => $depots]);
    }

    /**
     * GET /api/v1/depots/{depot}/tools — Tools at a specific depot.
     */
    public function tools(Depot $depot): JsonResponse
    {
        $tools = $depot->tools()
            ->where('status', '!=', ToolStatus::Archived->value)
            ->orderBy('name')
            ->paginate(15);

        return response()->json($tools);
    }
}
