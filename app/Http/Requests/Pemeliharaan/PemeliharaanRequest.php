<?php

namespace App\Http\Requests\Pemeliharaan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PemeliharaanRequest extends FormRequest
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
            'dates' => 'required',
            'departemen_id' => 'required'
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'dates.required' => 'Tanggal Pemeliharaan wajib diisi.',
            'departemen_id.required' => 'Lokasi Departemen wajib diisi.',
        ];
    }
}
