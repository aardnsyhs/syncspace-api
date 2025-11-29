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
      'name' => [
        'required',
        'string',
        'min:2',
        'max:100',
        'regex:/^[\pL\s\-\']+$/u', // Only letters, spaces, hyphens, apostrophes
      ],
      'email' => [
        'required',
        'string',
        'email:rfc',
        'max:255',
        'unique:users,email',
      ],
      'password' => [
        'required',
        'string',
        Password::min(8)
          ->letters()
          ->mixedCase()
          ->numbers(),
        'confirmed',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'name.regex' => 'Name can only contain letters, spaces, hyphens, and apostrophes.',
      'email.email' => 'Please provide a valid email address.',
      'password.min' => 'Password must be at least 8 characters.',
    ];
  }
}
