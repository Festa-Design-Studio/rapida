<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'phone_or_email_hash',
        'crisis_id',
        'badge_count',
        'leaderboard_score',
        'preferred_language',
        'verification_tier',
        'is_trusted_device',
        'trusted_since',
        'h3_cell_id',
        'is_suspended',
        'flagged_report_count',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_suspended' => 'boolean',
            'is_trusted_device' => 'boolean',
            'trusted_since' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class);
    }
}
