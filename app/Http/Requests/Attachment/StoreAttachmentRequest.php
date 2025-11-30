<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; 
  }

  public function rules(): array
  {
    
    if ($this->hasFile('file')) {
      return [
        'file' => [
          'required',
          'file',
          'max:10240', 
          'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
        ],
      ];
    }

    return [
      'url' => [
        'required',
        'url',
        'max:2048',
        'regex:/^https?:\/\//', 
      ],
      'file_name' => [
        'required',
        'string',
        'max:255',
        'regex:/^[\w\-. ]+$/', 
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
