<?php

namespace App\Models;

use App\Enums\WhatsappSessionState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'from_number',
        'crisis_slug',
        'state',
        'partial_data',
        'language',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'state' => WhatsappSessionState::class,
            'partial_data' => 'array',
            'expires_at' => 'datetime',
        ];
    }
}
