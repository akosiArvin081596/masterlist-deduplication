<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDuplicatePairRequest;
use App\Models\DuplicatePair;
use App\Models\Masterlist;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DuplicatePairController extends Controller
{
    public function index(Masterlist $masterlist): Response
    {
        $filter = request()->query('filter', 'all');

        $pairs = DuplicatePair::query()
            ->where('masterlist_id', $masterlist->id)
            ->when($filter !== 'all', fn ($q) => $q->where('status', $filter))
            ->with([
                'record1.masterlist',
                'record2.masterlist',
            ])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Masterlists/Review', [
            'masterlist' => $masterlist,
            'pairs' => $pairs,
            'filter' => $filter,
        ]);
    }

    public function update(UpdateDuplicatePairRequest $request, DuplicatePair $pair): RedirectResponse
    {
        $pair->update(['status' => $request->validated('status')]);

        return back();
    }
}
