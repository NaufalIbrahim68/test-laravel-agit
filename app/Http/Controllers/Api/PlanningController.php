<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlanningService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class PlanningController extends Controller
{
    private PlanningService $service;

    public function __construct(PlanningService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'requestCode' => 'required|string',
                'candidateToken' => 'nullable|string',
                'slots' => 'required|array|min:1',
                'slots.*.quantity' => 'required|numeric'
            ]);

            $candidateToken = $validated['candidateToken'] ?? 'VEH-TEST1234';
            $quantities = array_map(fn($slot) => (int) $slot['quantity'], $validated['slots']);

            $result = $this->service->processPlanning(
                $validated['requestCode'],
                $candidateToken,
                $quantities
            );

            $status = isset($result['created_at']) && empty($result['id']) ? 201 : 200;
            return response()->json($result, $status);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        $result = $this->service->getHistory($page, $perPage);
        return response()->json($result);
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->service->getDetail($id);

        if (!$result) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($result);
    }
}
