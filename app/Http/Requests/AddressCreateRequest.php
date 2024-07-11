<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['nullable', 'max:200'],
            'city' => ['nullable', 'max:100'],
            'province' => ['nullable', 'max:100'],
            'country' => ['required', 'max:100'],
            'postal_code' => ['nullable', 'max:10'],
        ];
    }

    public function messages()
    {
        return [
            'street.max' => 'Street maksimal 200 character',
            'city.max' => 'City maksimal 100 character',
            'province.max' => 'Province maksimal 100 character',
            'country.required' => 'Country tidak boleh kosong',
            'country.max' => 'Country maksimal 100 character',
            'postal_code.max' => 'Postal code maksimal 10 character',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
