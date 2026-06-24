<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9,10}$/'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'number_of_people' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:vnpay,bank_transfer'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
            'start_date.required' => 'Vui lòng chọn ngày khởi hành.',
            'start_date.after_or_equal' => 'Ngày khởi hành không được nhỏ hơn ngày hiện tại.',
            'number_of_people.required' => 'Vui lòng chọn số lượng khách.',
            'number_of_people.min' => 'Số lượng khách phải từ 1 người trở lên.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ];
    }
}
