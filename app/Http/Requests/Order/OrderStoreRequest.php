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

            'payment_method_id' => 'int|required',
            'delivery_address_id' => 'int|required',
            'shipping_method_id' => 'int|required',
//            'coupon'            => 'string',
        ];
    }

    public function messages()
    {
        return [
            'payment_method_id.required' => 'payment method id is required!',
            'payment_method_id.int' => 'payment method id must be an integer!',
            'delivery_address_id.required' => 'delivery address id is required!',
            'delivery_address_id.int' => 'delivery address id must be an integer!',
        ];
    }

}
