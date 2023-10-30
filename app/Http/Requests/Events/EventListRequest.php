<?php

namespace App\Http\Requests\Events;

use App\Enums\EventSearchMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EventListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dateFrom' => ['date', 'date_format:Y-m-d'],
            'dateTo' => ['date', 'date_format:Y-m-d', 'after_or_equal:dateFrom'],
            'mode' => [new Enum(EventSearchMode::class), 'required_with:dateFrom,dateTo']
        ];
    }
}
