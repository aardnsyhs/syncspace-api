<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'color' => ['nullable', 'string', 'max:7'],
    ];
  }
}
