<?php

namespace App\Models;

use App\Enums\DuplicatePairStatus;
use App\Enums\MatchType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicatePair extends Model
{
    protected $fillable = [
        'masterlist_id',
        'record_1_id',
        'record_2_id',
        'match_type',
        'confidence',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'match_type' => MatchType::class,
            'status' => DuplicatePairStatus::class,
        ];
    }

    public function masterlist(): BelongsTo
    {
        return $this->belongsTo(Masterlist::class);
    }

    public function record1(): BelongsTo
    {
        return $this->belongsTo(MasterlistRecord::class, 'record_1_id');
    }

    public function record2(): BelongsTo
    {
        return $this->belongsTo(MasterlistRecord::class, 'record_2_id');
    }
}
