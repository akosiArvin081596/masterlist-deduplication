<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'incident_name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'incident_name.required' => 'An incident name is required.',
            'file.required' => 'A CSV file is required.',
            'file.mimes' => 'The file must be a CSV or TXT file.',
            'file.max' => 'The file must not exceed 20 MB.',
        ];
    }
}
