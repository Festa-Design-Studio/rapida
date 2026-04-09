<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crisis extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'default_language',
        'available_languages',
        'active_modules',
        'map_tile_bbox',
        'h3_resolution',
        'status',
        'qr_code_url',
    ];

    protected function casts(): array
    {
        return [
            'available_languages' => 'array',
            'active_modules' => 'array',
            'map_tile_bbox' => 'array',
        ];
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    public function landmarks(): HasMany
    {
        return $this->hasMany(Landmark::class);
    }

    public function undpUsers(): HasMany
    {
        return $this->hasMany(UndpUser::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
