<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PaymentInquiryRequest extends FormRequest
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
        // Kode Pembayaran harus angka dan berjumlah 19 digit.
        return  [
            'kode_pembayaran' => ['required', 'string', 'size:19', 'regex:/^\d+$/'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_pembayaran.required' => 'Kode pembayaran harus diisi.',
            'kode_pembayaran.string' => 'Kode pembayaran harus berupa string.',
            'kode_pembayaran.size' => 'Kode pembayaran harus memiliki panjang 19 digit.',
            'kode_pembayaran.regex' => 'Kode pembayaran harus berupa angka saja.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            // 'success'   => false,
            'message'   => 'Request tidak memenuhi persyaratan. Silahkan periksa kembali request yang dikirim',
            'code'      => 400,
            'errors'     => $validator->errors(),
        ], 400));
    }
}
