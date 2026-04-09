<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'crisis_id',
        'ms_building_id',
        'canonical_damage_level',
        'canonical_report_id',
        'report_count',
        'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'last_updated_at' => 'datetime',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'building_footprint_id');
    }

    public function canonicalReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'canonical_report_id');
    }
}
