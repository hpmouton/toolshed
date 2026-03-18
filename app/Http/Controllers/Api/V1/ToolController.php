<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ToolStatus;
use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FR-15.2 — Tool API endpoints.
 */
class ToolController extends Controller
{
    /**
     * GET /api/v1/tools — Paginated tool list with search and filter support.
     */
    public function index(Request $request): JsonResponse
    {
        $tools = Tool::query()
            ->with('depot')
            ->where('status', '!=', ToolStatus::Archived->value)
            ->whereHas('depot', fn ($q) => $q->where('is_active', true))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('depot_id'), fn ($q) => $q->where('depot_id', $request->depot_id))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json($tools);
    }

    /**
     * GET /api/v1/tools/{tool} — Single tool record.
     */
    public function show(Tool $tool): JsonResponse
    {
        $tool->load('depot');

        return response()->json(['data' => $tool]);
    }
}
