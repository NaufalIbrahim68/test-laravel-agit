<?php

namespace App\Repositories;

use App\Models\Planning;
use App\Models\PlanningSlot;
use Illuminate\Support\Facades\DB;

class PlanningRepository
{
    public function findByRequestCode(string $code): ?Planning
    {
        return Planning::where('RequestCode', $code)->with('slots')->first();
    }

    public function createWithSlots(string $requestCode, string $candidateToken, array $slots): Planning
    {
        return DB::transaction(function () use ($requestCode, $candidateToken, $slots) {
            $planning = Planning::create([
                'RequestCode' => $requestCode,
                'CandidateToken' => $candidateToken,
            ]);

            foreach ($slots as $index => $qtyData) {
                $originalQty = is_array($qtyData) ? $qtyData['original'] : $qtyData;
                $balancedQty = is_array($qtyData) ? $qtyData['balanced'] : 0;
                
                PlanningSlot::create([
                    'PlanningId' => $planning->PlanningId,
                    'SlotOrder' => $index,
                    'SlotName' => 'Slot ' . ($index + 1),
                    'OriginalQuantity' => $originalQty,
                    'BalancedQuantity' => $balancedQty,
                    'IsActive' => $originalQty > 0,
                ]);
            }

            return $planning->load('slots');
        });
    }

    public function getHistory(int $limit = 10, int $offset = 0): array
    {
        $query = Planning::query();
        $total = $query->count();
        $data = $query->orderBy('CreatedAt', 'desc')->offset($offset)->limit($limit)->get();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function findById(int $id): ?Planning
    {
        return Planning::where('PlanningId', $id)->with('slots')->first();
    }
}
