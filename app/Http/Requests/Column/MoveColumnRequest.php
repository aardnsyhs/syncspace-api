<?php

namespace App\Http\Requests\Column;

use Illuminate\Foundation\Http\FormRequest;

class MoveColumnRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'position' => ['required', 'integer', 'min:0'],
    ];
  }
}
