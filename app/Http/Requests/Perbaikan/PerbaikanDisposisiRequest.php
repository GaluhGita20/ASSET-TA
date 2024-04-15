<?php

namespace App\Http\Requests\Perbaikan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PerbaikanDisposisiRequest extends FormRequest
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
            'perbaikan_id'=>'required',
            'repair_type' => 'required',
            'vendor_id' => 'required'
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'perbaikan_id.required' => 'code perbaikan wajib diisi.',
            'repair_type.required' => 'Jenis Perbaikan wajib diisi.',
            'vendor.required' => 'Vendor wajib diisi.',
        ];
    }
}
