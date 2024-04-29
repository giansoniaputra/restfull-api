<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactCreateRequest extends FormRequest
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
        $data =
            [
                'first_name' => ['required', 'max:100'],
                'last_name' => ['nullable', 'max:100'],
                'email' => ['nullable', 'email', 'max:100'],
                'phone' => ['nullable', 'max:100'],
            ];
        return $data;
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Nama depan wajib diisi.',
            'first_name.max' => 'Nama depan tidak boleh lebih dari 100 karakter.',
            'last_name.max' => 'Nama belakang tidak boleh lebih dari 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 100 karakter.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 100 karakter.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->errors()
        ], 400));
    }
}
