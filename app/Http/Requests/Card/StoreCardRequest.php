<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'assignee_id' => ['nullable', 'exists:users,id'],
      'due_date' => ['nullable', 'date'],
    ];
  }
}
