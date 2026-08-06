<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningSlot extends Model
{
    protected $table = 'planning_slots';
    public $timestamps = false;
    protected $fillable = [
        'PlanningId',
        'SlotOrder',
        'SlotName',
        'OriginalQuantity',
        'BalancedQuantity',
        'IsActive',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'OriginalQuantity' => 'integer',
        'BalancedQuantity' => 'integer',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class, 'PlanningId', 'PlanningId');
    }
}
