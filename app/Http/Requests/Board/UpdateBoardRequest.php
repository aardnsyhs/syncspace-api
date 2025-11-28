<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'color' => ['nullable', 'string', 'max:7'],
    ];
  }
}
