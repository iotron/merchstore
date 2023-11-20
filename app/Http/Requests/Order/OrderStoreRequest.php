<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
    public function rules()
    {
        return [

            'payment_provider_id' => 'int|required',
            'shipping_address_id' => 'int|required',
            'billing_address_id' => 'int|required_if:shipping_is_billing,false',
            'coupon'            => 'string|nullable',
            'shipping_is_billing' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'payment_provider_id.required' => 'payment method id is required!',
            'payment_provider_id.int' => 'payment method id must be an integer!',
            'shipping_address_id.required' => 'delivery address id is required!',
            'shipping_address_id.int' => 'delivery address id must be an integer!',

        ];
    }

}
