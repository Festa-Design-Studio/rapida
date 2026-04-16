<?php

namespace App\Models;

use App\Enums\DamageLevel;
use App\Enums\LocationMethod;
use App\Enums\SubmissionChannel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamageReport extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'crisis_id',
        'building_footprint_id',
        'account_id',
        'device_fingerprint_id',
        'landmark_id',
        'photo_url',
        'photo_hash',
        'photo_phash',
        'photo_size_bytes',
        'photo_guidance_shown',
        'damage_level',
        'ai_suggested_level',
        'ai_confidence',
        'infrastructure_type',
        'crisis_type',
        'infrastructure_name',
        'debris_required',
        'location_method',
        'latitude',
        'longitude',
        'w3w_code',
        'landmark_text',
        'h3_cell_id',
        'description',
        'description_original_lang',
        'description_en',
        'completeness_score',
        'submitted_via',
        'reporter_tier',
        'idempotency_key',
        'is_flagged',
        'submitted_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'damage_level' => DamageLevel::class,
            'ai_suggested_level' => DamageLevel::class,
            'location_method' => LocationMethod::class,
            'submitted_via' => SubmissionChannel::class,
            'debris_required' => 'boolean',
            'is_flagged' => 'boolean',
            'photo_guidance_shown' => 'boolean',
            'submitted_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function crisis(): BelongsTo
    {
        return $this->belongsTo(Crisis::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_footprint_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function landmark(): BelongsTo
    {
        return $this->belongsTo(Landmark::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ReportModule::class, 'report_id');
    }

    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class, 'report_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ReportVersion::class, 'report_id')->orderBy('version_number');
    }

    protected static function booted(): void
    {
        static::updating(function (DamageReport $report) {
            $changed = $report->getDirty();

            if (empty($changed)) {
                return;
            }

            $versionNumber = $report->versions()->count() + 1;

            ReportVersion::create([
                'report_id' => $report->id,
                'version_number' => $versionNumber,
                'changed_by_type' => auth('undp')->check() ? 'undp' : (auth()->check() ? 'account' : 'system'),
                'changed_by_id' => auth('undp')->id() ?? auth()->id(),
                'snapshot' => $report->getOriginal(),
                'changed_fields' => array_keys($changed),
                'created_at' => now(),
            ]);
        });
    }
}
