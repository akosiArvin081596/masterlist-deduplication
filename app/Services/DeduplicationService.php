<?php

namespace App\Services;

use App\Enums\DuplicatePairStatus;
use App\Enums\MatchType;
use App\Enums\MasterlistStatus;
use App\Models\Masterlist;
use App\Models\MasterlistRecord;
use Illuminate\Support\Collection;

class DeduplicationService
{
    public function run(Masterlist $masterlist): void
    {
        $masterlist->update(['status' => MasterlistStatus::Deduplicating]);

        $thisRecords = MasterlistRecord::where('masterlist_id', $masterlist->id)->get();
        $otherRecords = MasterlistRecord::where('masterlist_id', '!=', $masterlist->id)->get();

        $pairs = collect();

        // Within this masterlist
        for ($i = 0; $i < $thisRecords->count(); $i++) {
            for ($j = $i + 1; $j < $thisRecords->count(); $j++) {
                $match = $this->compare($thisRecords[$i], $thisRecords[$j]);
                if ($match !== null) {
                    $pairs->push([
                        'masterlist_id' => $masterlist->id,
                        'record_1_id' => $thisRecords[$i]->id,
                        'record_2_id' => $thisRecords[$j]->id,
                        'match_type' => $match['type']->value,
                        'confidence' => $match['confidence'],
                        'status' => DuplicatePairStatus::Pending->value,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                }
            }
        }

        // Against other masterlists
        foreach ($thisRecords as $r1) {
            foreach ($otherRecords as $r2) {
                $match = $this->compare($r1, $r2);
                if ($match !== null) {
                    $pairs->push([
                        'masterlist_id' => $masterlist->id,
                        'record_1_id' => $r1->id,
                        'record_2_id' => $r2->id,
                        'match_type' => $match['type']->value,
                        'confidence' => $match['confidence'],
                        'status' => DuplicatePairStatus::Pending->value,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                }
            }
        }

        foreach ($pairs->chunk(500) as $chunk) {
            \App\Models\DuplicatePair::upsert(
                $chunk->values()->all(),
                ['record_1_id', 'record_2_id'],
                ['match_type', 'confidence', 'updated_at']
            );
        }

        $masterlist->update([
            'status' => MasterlistStatus::Completed,
            'duplicate_pair_count' => $pairs->count(),
        ]);
    }

    /** @return array{type: MatchType, confidence: int}|null */
    private function compare(MasterlistRecord $r1, MasterlistRecord $r2): ?array
    {
        $name1 = strtolower(trim("{$r1->last_name} {$r1->first_name} {$r1->middle_name}"));
        $name2 = strtolower(trim("{$r2->last_name} {$r2->first_name} {$r2->middle_name}"));

        $birthdayMatch = $r1->birthday !== null && $r2->birthday !== null && $r1->birthday->eq($r2->birthday);
        $birthdayNear = $r1->birthday !== null && $r2->birthday !== null && abs($r1->birthday->diffInDays($r2->birthday)) <= 365;

        if ($name1 === $name2 && $birthdayMatch) {
            return ['type' => MatchType::Exact, 'confidence' => 100];
        }

        $maxLen = max(strlen($name1), strlen($name2));

        if ($maxLen > 0) {
            similar_text($name1, $name2, $percent);
            $similarity = $percent / 100;

            if ($similarity >= 0.85 && ($birthdayMatch || $birthdayNear)) {
                return ['type' => MatchType::Fuzzy, 'confidence' => 90];
            }
        }

        if (levenshtein($name1, $name2) <= 3 && ($birthdayMatch || $birthdayNear)) {
            return ['type' => MatchType::Typo, 'confidence' => 75];
        }

        return null;
    }
}
