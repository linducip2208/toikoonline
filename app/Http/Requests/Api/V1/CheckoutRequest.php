<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'required|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'shipping_method' => 'required|string|max:128',
            'courier' => 'nullable|string|max:64',
            'shipping_cost' => 'nullable|integer|min:0',
            'payment_gateway_id' => 'required|integer|exists:payment_gateway_configs,id',
            'coupon_code' => 'nullable|string|max:64',
            'additional_info' => 'nullable|string|max:2000',
            'pickup_point_id' => 'nullable|integer|exists:pickup_points,id',
        ];
    }
}
