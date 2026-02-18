<?php

namespace App\Jobs;

use App\Models\Masterlist;
use App\Services\DeduplicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunDeduplication implements ShouldQueue
{
    use Queueable;

    public function __construct(public Masterlist $masterlist) {}

    public function handle(DeduplicationService $deduplicationService): void
    {
        $deduplicationService->run($this->masterlist);
    }
}
