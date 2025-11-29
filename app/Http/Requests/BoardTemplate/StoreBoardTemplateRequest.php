<?php

// app/Http/Requests/BoardTemplate/StoreBoardTemplateRequest.php

namespace App\Http\Requests\BoardTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardTemplateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // Authorization handled by policy
  }

  public function rules(): array
  {
    return [
      'name' => [
        'required',
        'string',
        'min:2',
        'max:100',
      ],
      'description' => [
        'nullable',
        'string',
        'max:500',
      ],
      'board_id' => [
        'required',
        'integer',
        'exists:boards,id',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'Template name is required.',
      'name.max' => 'Template name cannot exceed 100 characters.',
      'board_id.exists' => 'The specified board does not exist.',
    ];
  }
}
