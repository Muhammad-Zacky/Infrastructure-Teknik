<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfrastructureRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $infrastructureId = $this->route('infrastructure')->id;

        $rules = [
            'category' => 'required|in:equipment,facility,utility',
            'code_name' => 'required|string|max:50|unique:infrastructures,code_name,' . $infrastructureId,
            'status' => 'required|in:available,breakdown',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        // Add entity_id validation for superadmin
        if (auth()->user()->role === 'superadmin') {
            $rules['entity_id'] = 'required|integer|exists:entities,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Kategori infrastruktur harus dipilih',
            'category.in' => 'Kategori tidak valid. Pilih: equipment, facility, atau utility',
            'code_name.required' => 'Kode nama aset harus diisi',
            'code_name.unique' => 'Kode nama aset sudah digunakan, gunakan kode lain',
            'code_name.max' => 'Kode nama aset maksimal 50 karakter',
            'status.required' => 'Status aset harus dipilih',
            'status.in' => 'Status aset tidak valid. Pilih: available atau breakdown',
            'entity_id.required' => 'Entitas/Cabang harus dipilih (Superadmin)',
            'entity_id.exists' => 'Entitas/Cabang yang dipilih tidak ditemukan',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus: JPEG, PNG, JPG, atau WebP',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
