<?php

namespace App\Jobs;

use App\Enums\MasterlistStatus;
use App\Models\Masterlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ProcessMasterlist implements ShouldQueue
{
    use Queueable;

    public function __construct(public Masterlist $masterlist) {}

    public function handle(): void
    {
        $this->masterlist->update(['status' => MasterlistStatus::Processing]);

        $path = Storage::path($this->masterlist->original_filename);
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return;
        }

        // Skip header row
        fgetcsv($handle);

        $records = [];
        $count = 0;
        $now = now()->toDateTimeString();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 10) {
                continue;
            }

            [$lastName, $firstName, $middleName, $extName, $birthday, $regionName, $provinceName, $cityName, $barangayName, $purokSitio] = array_pad($row, 10, null);

            $birthdayParsed = null;
            if (! empty($birthday)) {
                try {
                    $birthdayParsed = Carbon::parse($birthday)->toDateString();
                } catch (\Exception) {
                    $birthdayParsed = null;
                }
            }

            $records[] = [
                'masterlist_id' => $this->masterlist->id,
                'last_name' => trim((string) $lastName),
                'first_name' => trim((string) $firstName),
                'middle_name' => trim((string) $middleName) ?: null,
                'ext_name' => trim((string) $extName) ?: null,
                'birthday' => $birthdayParsed,
                'region_name' => trim((string) $regionName) ?: null,
                'province_name' => trim((string) $provinceName) ?: null,
                'city_name' => trim((string) $cityName) ?: null,
                'barangay_name' => trim((string) $barangayName) ?: null,
                'purok_sitio' => trim((string) $purokSitio) ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;

            if (count($records) >= 500) {
                \App\Models\MasterlistRecord::insert($records);
                $records = [];
            }
        }

        fclose($handle);

        if (! empty($records)) {
            \App\Models\MasterlistRecord::insert($records);
        }

        $this->masterlist->update([
            'status' => MasterlistStatus::Ready,
            'record_count' => $count,
        ]);
    }
}
