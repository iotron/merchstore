<?php

namespace App\Http\Requests\Location\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressStoreRequest extends FormRequest
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
            'name' => 'string|required',
            'contact' => 'string|required|max:15|min:10',
            'alternate_contact' => 'string|nullable|max:15|min:10',
            'type' => 'in:Home,Work,Other|required',
            'address_1' => 'string|required',
            'address_2' => 'string|nullable',
            'landmark' => 'string|nullable',
            'city' => 'string|required',
            'postal_code' => 'string|required',
            'state' => 'string|required',
            'default' => 'boolean',
            'priority' => 'integer',
            'country_code' => 'string|nullable|exists:countries,iso_code_2',
        ];
    }
}
