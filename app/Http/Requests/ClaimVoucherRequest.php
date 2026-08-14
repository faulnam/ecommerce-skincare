<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'voucher_id' => 'required|integer|exists:vouchers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'voucher_id.required' => 'Voucher ID is required',
            'voucher_id.exists' => 'Voucher not found',
        ];
    }
}
