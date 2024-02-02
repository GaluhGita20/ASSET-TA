<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PerbaikanRequest extends FormRequest
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
            'problem' => 'required',
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'problem.required' => 'Keluhan wajib diisi.',
        ];
    }
}
