<?php

namespace App\Models;

use App\Enums\MasterlistStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Masterlist extends Model
{
    protected $fillable = [
        'user_id',
        'incident_name',
        'original_filename',
        'status',
        'record_count',
        'duplicate_pair_count',
    ];

    protected function casts(): array
    {
        return [
            'status' => MasterlistStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(MasterlistRecord::class);
    }

    public function duplicatePairs(): HasMany
    {
        return $this->hasMany(DuplicatePair::class);
    }
}
