<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderConfirmRequest extends FormRequest
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
            'razorpay_order_id' => 'required_with:razorpay_payment_id,razorpay_signature|string',
            'razorpay_payment_id' => 'required_with:razorpay_order_id,razorpay_signature|string',
            'razorpay_signature' => 'required_with:razorpay_order_id,razorpay_payment_id|string',
            'session_id' => 'nullable|string',
        ];
    }
}
