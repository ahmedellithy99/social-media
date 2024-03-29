<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class PostRequest extends FormRequest
{

    public static array $extensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'mp3', 'wav', 'mp4',
        "doc", "docx", "csv", "xls", "xlsx",
        "zip"
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'nullable',
            'attachments' => [
                'array',
                'max:50',
                function ($attribute, $value, $fail) {
                    // Custom rule to check the total size of all files
                    $totalSize = collect($value)->sum(fn (UploadedFile $file) => $file->getSize());

                    if ($totalSize > 1 * 1024 * 1024 * 1024) {
                        $fail('The total size of all files must not exceed 1GB.');
                    }
                },
            ],
            'attachments.*' => ['file', File::types(self::$extensions), 'max: 50000'],
            'user_id' => 'numeric'
        ];
    }

    protected function prepareForValidation()
    {
        // Add your custom key to the request data
        $this->merge([
            'body' => $this->input('body') ?: '',
            'user_id' => auth()->user()->id
        ]);
    }

    public function messages()
    {
        return [
            'attachments.*.file' => 'Each attachment must be a file.',
            'attachments.*.mimes' => 'Invalid file type for attachments.',
            'attachments.*.max' => 'Yoy must not exceed 50MB File'
        ];
    }
}
