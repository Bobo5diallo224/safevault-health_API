<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],

            'role' => [
                'required',
                'string',
                'in:patient,doctor',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',
            'password.min'        => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.uncompromised' => 'Ce mot de passe a été compromis dans une fuite de données. Choisissez-en un autre.',
            'role.in'             => 'Le rôle doit être patient ou doctor.',
        ];
    }
}