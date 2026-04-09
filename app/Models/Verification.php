<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'assigned_to',
        'status',
        'notes',
        'assigned_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => VerificationStatus::class,
            'assigned_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(UndpUser::class, 'assigned_to');
    }
}
