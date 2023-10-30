<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class EventCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:256'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date', 'date_format:Y-m-d'],
            'description' => ['required', 'string', 'max:65536'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'image' => ['mimes:png,jpg']
        ];
    }
}
