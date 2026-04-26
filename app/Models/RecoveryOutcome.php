<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RecoveryOutcome extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['crisis_id', 'h3_cell_id', 'message', 'triggered_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('recovery_outcome');
    }

    protected $fillable = [
        'crisis_id',
        'h3_cell_id',
        'message',
        'triggered_by',
        'triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'triggered_at' => 'datetime',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(UndpUser::class, 'triggered_by');
    }
}
