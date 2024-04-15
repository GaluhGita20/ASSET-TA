<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class HasilPerbaikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    
    public function rules()
    {
        $id = $this->record->id ?? 0;

        $rules= [
            // 'kib_id'=>'required',
            'check_up_result' => 'required',
            'is_disposisi' => 'required',
            // 'user_id' => 'required'
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'check_up_result.required' => 'Hasil Pemeriksaan Awal wajib diisi.',
            'is_disposisi.required' => 'Status Pengajuan Disposisi Perlu diisi.',
            // 'user_id.required' => 'Petugas Perbaikan wajib diisi.',
        ];
    }
}
