<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterlistRecord extends Model
{
    protected $fillable = [
        'masterlist_id',
        'last_name',
        'first_name',
        'middle_name',
        'ext_name',
        'birthday',
        'region_name',
        'province_name',
        'city_name',
        'barangay_name',
        'purok_sitio',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date:Y-m-d',
        ];
    }

    public function masterlist(): BelongsTo
    {
        return $this->belongsTo(Masterlist::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->last_name} {$this->first_name} {$this->middle_name}"),
        );
    }
}
