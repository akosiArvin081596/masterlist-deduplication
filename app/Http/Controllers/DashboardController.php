<?php

namespace App\Http\Controllers;

use App\Enums\DuplicatePairStatus;
use App\Enums\MasterlistStatus;
use App\Models\DuplicatePair;
use App\Models\Masterlist;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $userId = auth()->id();

        $masterlists = Masterlist::where('user_id', $userId);

        $totalRecords = (clone $masterlists)->sum('record_count');
        $totalPairs = (clone $masterlists)->sum('duplicate_pair_count');

        $confirmedPairs = DuplicatePair::whereHas('masterlist', fn ($q) => $q->where('user_id', $userId))
            ->where('status', DuplicatePairStatus::Confirmed)
            ->count();

        $pendingPairs = DuplicatePair::whereHas('masterlist', fn ($q) => $q->where('user_id', $userId))
            ->where('status', DuplicatePairStatus::Pending)
            ->count();

        $resolvedPairs = DuplicatePair::whereHas('masterlist', fn ($q) => $q->where('user_id', $userId))
            ->whereIn('status', [DuplicatePairStatus::Confirmed, DuplicatePairStatus::Dismissed])
            ->count();

        $processedRecords = (clone $masterlists)->where('status', MasterlistStatus::Completed)->sum('record_count');

        $cleanRecords = max(0, $totalRecords - $confirmedPairs);

        $lastCompleted = (clone $masterlists)
            ->where('status', MasterlistStatus::Completed)
            ->latest('updated_at')
            ->first();

        $isActive = (clone $masterlists)
            ->whereIn('status', [MasterlistStatus::Processing, MasterlistStatus::Deduplicating])
            ->exists();

        $recentMasterlists = (clone $masterlists)
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $recentActivity = $recentMasterlists->map(function (Masterlist $masterlist): array {
            return match ($masterlist->status) {
                MasterlistStatus::Completed => [
                    'action' => 'Deduplication completed',
                    'detail' => "{$masterlist->incident_name} — {$masterlist->record_count} records, {$masterlist->duplicate_pair_count} pairs found",
                    'time' => $masterlist->updated_at->diffForHumans(),
                    'status' => 'success',
                ],
                MasterlistStatus::Ready => [
                    'action' => 'Masterlist ready',
                    'detail' => "{$masterlist->incident_name} — {$masterlist->record_count} records parsed",
                    'time' => $masterlist->updated_at->diffForHumans(),
                    'status' => 'success',
                ],
                MasterlistStatus::Deduplicating => [
                    'action' => 'Deduplication running',
                    'detail' => "{$masterlist->incident_name} — in progress",
                    'time' => $masterlist->updated_at->diffForHumans(),
                    'status' => 'warning',
                ],
                MasterlistStatus::Processing => [
                    'action' => 'Masterlist processing',
                    'detail' => "{$masterlist->incident_name} — parsing CSV",
                    'time' => $masterlist->updated_at->diffForHumans(),
                    'status' => 'warning',
                ],
                default => [
                    'action' => 'Masterlist uploaded',
                    'detail' => $masterlist->incident_name,
                    'time' => $masterlist->created_at->diffForHumans(),
                    'status' => 'success',
                ],
            };
        })->values()->all();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalRecords' => (int) $totalRecords,
                'totalPairs' => (int) $totalPairs,
                'processedRecords' => (int) $processedRecords,
                'cleanRecords' => (int) $cleanRecords,
                'confirmedPairs' => $confirmedPairs,
                'pendingPairs' => $pendingPairs,
                'resolvedPairs' => $resolvedPairs,
            ],
            'recentActivity' => $recentActivity,
            'processingStatus' => [
                'processedPercent' => $totalRecords > 0 ? round($processedRecords / $totalRecords * 100, 1) : 0,
                'resolvedPercent' => $totalPairs > 0 ? round($resolvedPairs / $totalPairs * 100, 1) : 0,
                'pendingPercent' => $totalPairs > 0 ? round($pendingPairs / $totalPairs * 100, 1) : 0,
                'lastRun' => $lastCompleted?->updated_at->diffForHumans() ?? 'Never',
                'isActive' => $isActive,
            ],
        ]);
    }
}
