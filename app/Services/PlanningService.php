<?php

namespace App\Services;

use App\Repositories\PlanningRepository;
use App\Logic\BalancingLogic;
use Illuminate\Validation\ValidationException;

class PlanningService
{
    private PlanningRepository $repository;

    public function __construct(PlanningRepository $repository)
    {
        $this->repository = $repository;
    }

    public function processPlanning(string $requestCode, string $candidateToken, array $quantities): array
    {
        $existing = $this->repository->findByRequestCode($requestCode);
        if ($existing) {
            return $this->formatPlanningData($existing);
        }

        $errors = BalancingLogic::validate($quantities);
        if (!empty($errors)) {
            throw ValidationException::withMessages(['slots' => $errors]);
        }

        $balancedQuantities = BalancingLogic::balance($quantities);

        $slotsData = [];
        foreach ($quantities as $index => $qty) {
            $slotsData[] = [
                'original' => $qty,
                'balanced' => $balancedQuantities[$index] ?? 0,
            ];
        }

        $planning = $this->repository->createWithSlots($requestCode, $candidateToken, $slotsData);

        return $this->formatPlanningData($planning);
    }

    public function getHistory(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $result = $this->repository->getHistory($perPage, $offset);

        $formattedData = [];
        foreach ($result['data'] as $item) {
            $formattedData[] = $this->formatPlanningData($item);
        }

        return [
            'data' => $formattedData,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $result['total'],
                'last_page' => ceil($result['total'] / max($perPage, 1))
            ]
        ];
    }

    public function getDetail(int $id): ?array
    {
        $planning = $this->repository->findById($id);
        if (!$planning) {
            return null;
        }

        return $this->formatPlanningData($planning);
    }

    private function formatPlanningData($planning): array
    {
        $originalTotal = 0;
        $balancedTotal = 0;
        $slots = [];

        if ($planning->slots) {
            foreach ($planning->slots as $slot) {
                $originalTotal += $slot->OriginalQuantity;
                $balancedTotal += $slot->BalancedQuantity;
                $slots[] = [
                    'id' => $slot->id,
                    'name' => $slot->SlotName,
                    'original' => $slot->OriginalQuantity,
                    'balanced' => $slot->BalancedQuantity,
                    'is_active' => $slot->IsActive,
                ];
            }
        }

        return [
            'id' => $planning->PlanningId,
            'request_code' => $planning->RequestCode,
            'candidate_token' => $planning->CandidateToken,
            'status' => $planning->Status,
            'created_at' => $planning->CreatedAt,
            'original_total' => $originalTotal,
            'balanced_total' => $balancedTotal,
            'slots' => $slots,
        ];
    }
}
