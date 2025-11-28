<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class MoveCardRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'column_id' => ['required', 'exists:columns,id'],
      'position' => ['required', 'integer', 'min:0'],
    ];
  }
}
