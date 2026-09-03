<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $voucherId = $this->route('id');

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:vouchers,slug,' . $voucherId,
            'code' => 'nullable|string|max:50|unique:vouchers,code,' . $voucherId,
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent,cashback',
            'category' => 'required|in:all,serum,moisturizer,cleanser,toner,sunscreen,new_arrivals,hijab,accessories,shoes',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'cashback_coin' => 'nullable|integer|min:0',
            'quota' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_displayed' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Voucher title is required',
            'type.required' => 'Voucher type is required',
            'type.in' => 'Voucher type must be fixed, percent, or cashback',
            'discount_value.required' => 'Discount value is required',
            'minimum_purchase.required' => 'Minimum purchase is required',
            'quota.required' => 'Quota is required',
            'quota.min' => 'Quota must be at least 1',
            'start_date.required' => 'Start date is required',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
            'thumbnail.image' => 'Thumbnail must be an image',
            'thumbnail.mimes' => 'Thumbnail must be a jpeg, png, jpg, gif, or webp file',
            'thumbnail.max' => 'Thumbnail must not exceed 2MB',
        ];
    }
}
