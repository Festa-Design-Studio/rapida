<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportVersion extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'version_number',
        'changed_by_type',
        'changed_by_id',
        'snapshot',
        'changed_fields',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'changed_fields' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }
}
