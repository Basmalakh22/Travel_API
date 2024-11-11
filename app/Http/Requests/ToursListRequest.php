<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToursListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'priceFrom' => 'numeric',
            'priceTo' => 'numeric',
            'dateFrom' => 'date',
            'dateTo' => 'date',
            'sortBy' => Rule::in(['price' ,'starting_date']),
            'sortOrder' => Rule::in(['asc', 'desc']),
        ];
    }
    public function messages()
    {
        return [
            'sortBy' => "The 'sortBy' parameter accepts only 'price' values",
            'sortOrder' => "The 'sortOrder' parameter accepts only 'asc' or 'desc' values",
        ];
    }
}
