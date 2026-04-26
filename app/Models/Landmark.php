<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Landmark extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'latitude', 'longitude', 'crisis_id', 'osm_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('landmark');
    }

    protected $fillable = [
        'crisis_id',
        'name',
        'type',
        'latitude',
        'longitude',
        'added_by',
        'osm_id',
    ];

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(UndpUser::class, 'added_by');
    }
}
