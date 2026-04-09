<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Landmark extends Model
{
    use HasFactory, HasUuids;

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
