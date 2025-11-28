<?php

namespace App\Http\Requests\Column;

use Illuminate\Foundation\Http\FormRequest;

class StoreColumnRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
    ];
  }
}
