<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportModule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'module_key',
        'field_key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }
}
