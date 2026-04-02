<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CreateGrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => [
                'required',
                'string',
                'exists:users,id',               
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (! $user || ! $user->isDoctor()) {
                        $fail('L\'identifiant fourni ne correspond pas à un médecin.');
                    }
                },
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:now',
                'before:' . Carbon::now()->addYear()->toDateTimeString(),
            ],
        ];
    }
}