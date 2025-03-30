<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPositionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'The date parameter is required.',
            'date.date_format' => 'The date must be in the format YYYY-MM-DD.',
        ];
    }
}
