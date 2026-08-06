<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $table = 'plannings';
    protected $primaryKey = 'PlanningId';
    public $timestamps = false;
    protected $fillable = [
        'RequestCode',
        'CandidateToken',
        'CreatedAt',
        'Status',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(PlanningSlot::class, 'PlanningId', 'PlanningId');
    }
}
