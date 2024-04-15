<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class HasilPerbaikan2Request extends FormRequest
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
            'repair_results' => 'required',
            'action_repair' => 'required',
            'user_id' =>'required',
            // 'user_id' => 'required'
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            'repair_results.required' => 'Hasil Perbaikan Wajib diisi.',
            'action_repair.required' => 'Tindakan Perbaikan Wajib diisi.',
            'user_id.required' => 'Penanggung Jawab Perbaikan Wajib diisi.',
        ];
    }
}
