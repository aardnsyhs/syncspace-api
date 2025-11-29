<?php

// app/Http/Requests/Attachment/StoreAttachmentRequest.php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // Authorization handled by policy
  }

  public function rules(): array
  {
    // Either file upload OR external URL
    if ($this->hasFile('file')) {
      return [
        'file' => [
          'required',
          'file',
          'max:10240', // 10MB max
          'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ],
      ];
    }

    return [
      'url' => [
        'required',
        'url',
        'max:2048',
        'regex:/^https?:\/\//', // Must be http or https
      ],
      'file_name' => [
        'required',
        'string',
        'max:255',
        'regex:/^[\w\-. ]+$/', // Safe filename characters
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'file.max' => 'File size cannot exceed 10MB.',
      'file.mimes' => 'File type not allowed. Allowed: images, PDF, Office documents, text, archives.',
      'url.regex' => 'URL must start with http:// or https://',
      'file_name.regex' => 'File name contains invalid characters.',
    ];
  }
}
