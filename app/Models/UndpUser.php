<?php

namespace App\Models;

use App\Enums\UndpUserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UndpUser extends Authenticatable
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'crisis_id',
        'is_active',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role' => UndpUserRole::class,
            'is_active' => 'boolean',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'assigned_to');
    }

    public function landmarks(): HasMany
    {
        return $this->hasMany(Landmark::class, 'added_by');
    }
}
