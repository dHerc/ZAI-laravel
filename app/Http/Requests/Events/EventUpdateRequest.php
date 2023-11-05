<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'min:1', 'max:256'],
            'start_date' => ['date', 'date_format:Y-m-d'],
            'end_date' => ['date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'description' => ['string', 'max:15000'],
            'category_id' => ['integer', 'exists:categories,id'],
            'image' => ['nullable', 'mimes:png,jpg']
        ];
    }
}
