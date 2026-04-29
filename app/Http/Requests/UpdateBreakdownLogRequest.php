<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBreakdownLogRequest extends FormRequest
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
            'repair_status' => 'required|in:reported,order_part,on_progress,resolved',
            'document_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'troubleshoot_date' => 'nullable|date_format:Y-m-d',
            'ba_date' => 'nullable|date_format:Y-m-d',
            'work_order_date' => 'nullable|date_format:Y-m-d',
            'pr_po_date' => 'nullable|date_format:Y-m-d',
            'sparepart_date' => 'nullable|date_format:Y-m-d',
            'start_work_date' => 'nullable|date_format:Y-m-d|after_or_equal:troubleshoot_date',
            'com_test_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_work_date',
            'resolved_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_work_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'repair_status.required' => 'Status perbaikan harus dipilih',
            'repair_status.in' => 'Status perbaikan tidak valid. Pilih: reported, order_part, on_progress, atau resolved',
            'document_proof.file' => 'File harus berupa dokumen yang valid',
            'document_proof.mimes' => 'File dokumen harus berformat: PDF, JPG, JPEG, atau PNG',
            'document_proof.max' => 'Ukuran file dokumen maksimal 5MB',
            'troubleshoot_date.date_format' => 'Format tanggal troubleshoot tidak valid (Y-m-d)',
            'ba_date.date_format' => 'Format tanggal BA tidak valid (Y-m-d)',
            'work_order_date.date_format' => 'Format tanggal work order tidak valid (Y-m-d)',
            'pr_po_date.date_format' => 'Format tanggal PR/PO tidak valid (Y-m-d)',
            'sparepart_date.date_format' => 'Format tanggal sparepart tidak valid (Y-m-d)',
            'start_work_date.date_format' => 'Format tanggal mulai kerja tidak valid (Y-m-d)',
            'start_work_date.after_or_equal' => 'Tanggal mulai kerja tidak boleh sebelum tanggal troubleshoot',
            'com_test_date.date_format' => 'Format tanggal commissioning test tidak valid (Y-m-d)',
            'com_test_date.after_or_equal' => 'Tanggal commissioning test tidak boleh sebelum tanggal mulai kerja',
            'resolved_date.date_format' => 'Format tanggal selesai tidak valid (Y-m-d)',
            'resolved_date.after_or_equal' => 'Tanggal selesai pekerjaan tidak boleh sebelum tanggal mulai kerja',
        ];
    }
}
