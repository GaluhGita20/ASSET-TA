<?php

namespace App\Http\Requests\Pemeliharaan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PemeliharaanDetailRequest extends FormRequest
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
            'first_condition' => 'required',
            'latest_condition' => 'required',
            'maintenance_action' => 'required',
            'repair_officer' => 'required'
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            'first_condition.required' => 'Kondisi Awal Aset wajib diisi.',
            'latest_condition.required' => 'Kondisi Akhir Aset wajib diisi.',
            'maintenance_action.required' => 'Tindakan Pemeliharaan wajib diisi.',
            'repair_officer.required' => 'Petugas Pemeliharaan wajib diisi.',
        ];
    }
}
