<?php

namespace App\Models;

use Database\Factories\DangerZoneFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Operator-flagged H3 cell warning. Surfaces "this area is currently flagged
 * for X" hints to reporters whose location falls in or near the cell, and to
 * the public map's danger-zones layer. UNDP webinar named information-as-
 * service incentives — non-monetary value the reporter receives in return
 * for their report.
 *
 * Trauma-informed defaults: severity is "caution" by default, never alarming.
 * Conflict-mode crises never expose danger zones (DangerZoneService gates).
 */
class DangerZone extends Model
{
    /** @use HasFactory<DangerZoneFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'crisis_id',
        'h3_cell_id',
        'severity',
        'note',
        'created_by',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UndpUser::class, 'created_by');
    }

    /**
     * True if the zone is currently active (not yet expired). Used by the
     * service to filter the API response so reporters only see live warnings.
     */
    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
