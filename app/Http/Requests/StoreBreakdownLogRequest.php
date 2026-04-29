<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreakdownLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization check di controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'infrastructure_id' => 'required|integer|exists:infrastructures,id',
            'issue_detail' => 'required|string|max:500|min:5',
            'vendor_pic' => 'nullable|string|max:255|min:2',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'infrastructure_id.required' => 'Aset/Infrastruktur harus dipilih',
            'infrastructure_id.exists' => 'Aset yang dipilih tidak ditemukan',
            'issue_detail.required' => 'Deskripsi kerusakan harus diisi',
            'issue_detail.max' => 'Deskripsi kerusakan maksimal 500 karakter',
            'issue_detail.min' => 'Deskripsi kerusakan minimal 5 karakter',
            'vendor_pic.required' => 'Nama vendor/PIC harus diisi',
            'vendor_pic.max' => 'Nama vendor/PIC maksimal 255 karakter',
            'vendor_pic.min' => 'Nama vendor/PIC minimal 2 karakter',
        ];
    }
}
