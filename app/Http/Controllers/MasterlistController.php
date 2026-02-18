<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterlistRequest;
use App\Jobs\ProcessMasterlist;
use App\Models\Masterlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MasterlistController extends Controller
{
    public function index(): Response
    {
        $masterlists = Masterlist::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return Inertia::render('Masterlists/Index', [
            'masterlists' => $masterlists,
        ]);
    }

    public function store(StoreMasterlistRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $storedPath = $file->store('masterlists');

        $masterlist = Masterlist::create([
            'user_id' => auth()->id(),
            'incident_name' => $request->validated('incident_name'),
            'original_filename' => $storedPath,
            'status' => \App\Enums\MasterlistStatus::Pending,
        ]);

        ProcessMasterlist::dispatch($masterlist);

        return back();
    }
}
