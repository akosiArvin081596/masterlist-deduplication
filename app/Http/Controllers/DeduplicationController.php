<?php

namespace App\Http\Controllers;

use App\Enums\MasterlistStatus;
use App\Jobs\RunDeduplication;
use App\Models\Masterlist;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DeduplicationController extends Controller
{
    public function run(Masterlist $masterlist): RedirectResponse
    {
        abort_unless(
            in_array($masterlist->status, [MasterlistStatus::Ready, MasterlistStatus::Completed]),
            Response::HTTP_FORBIDDEN,
            'Deduplication can only be run on ready or completed masterlists.'
        );

        RunDeduplication::dispatch($masterlist);

        return back();
    }
}
