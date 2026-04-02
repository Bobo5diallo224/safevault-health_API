<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class CreateRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'patient_id' => [
                'required',
                'string',
                'exists:patient_profiles,id',
            ],
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[^<>{}]*$/',
            ],

            'diagnosis' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'treatment' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],

            'files_path' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'     => strip_tags($this->title ?? ''),
            'diagnosis' => strip_tags($this->diagnosis ?? ''),
            'treatment' => strip_tags($this->treatment ?? ''),
        ]);
    }
}