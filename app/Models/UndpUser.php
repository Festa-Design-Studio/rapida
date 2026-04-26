<?php

namespace App\Models;

use App\Enums\UndpUserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class UndpUser extends Authenticatable
{
    use HasFactory, HasUuids, LogsActivity, SoftDeletes;

    /**
     * Spatie/laravel-activitylog: every operator/staff mutation produces
     * an audit row. CRITICAL: `password` is excluded — under no
     * circumstances may bcrypt hashes leak into the activity_log table
     * (queryable by analyst dashboard). `remember_token` is also out.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'crisis_id', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('undp_user');
    }

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
