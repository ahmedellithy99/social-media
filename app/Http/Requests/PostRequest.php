<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class PostRequest extends FormRequest
{
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
            'body' => 'required',
            'attachments' => 'array|max:30' ,
            'attachments.*' => ['file' , File::types([
                'jpg', 'jpeg', 'png', 'gif', 'webp',
                'mp3', 'wav', 'mp4',
                "doc", "docx", "pdf", "csv", "xls", "xlsx",
                "zip"
            ])],
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
}
