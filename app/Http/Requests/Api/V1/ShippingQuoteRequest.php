<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ShippingQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin' => 'nullable',
            'destination' => 'required',
            'weight' => 'required|integer|min:1|max:30000',
            'couriers' => 'nullable|string|max:255',
        ];
    }
}
