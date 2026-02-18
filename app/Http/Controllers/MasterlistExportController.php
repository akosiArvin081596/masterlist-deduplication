<?php

namespace App\Http\Controllers;

use App\Enums\DuplicatePairStatus;
use App\Models\Masterlist;
use App\Models\MasterlistRecord;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterlistExportController extends Controller
{
    public function __invoke(Request $request, Masterlist $masterlist): StreamedResponse
    {
        $confirmedRecordIds = $masterlist->duplicatePairs()
            ->where('status', DuplicatePairStatus::Confirmed)
            ->pluck('record_2_id');

        return response()->streamDownload(function () use ($masterlist, $confirmedRecordIds): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
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
            ]);

            MasterlistRecord::where('masterlist_id', $masterlist->id)
                ->whereNotIn('id', $confirmedRecordIds)
                ->orderBy('id')
                ->chunk(500, function ($records) use ($handle): void {
                    foreach ($records as $record) {
                        fputcsv($handle, [
                            $record->last_name,
                            $record->first_name,
                            $record->middle_name,
                            $record->ext_name,
                            $record->birthday?->toDateString(),
                            $record->region_name,
                            $record->province_name,
                            $record->city_name,
                            $record->barangay_name,
                            $record->purok_sitio,
                        ]);
                    }
                });

            fclose($handle);
        }, "{$masterlist->incident_name}_clean.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }
}
