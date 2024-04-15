<?php

namespace App\Http\Requests\Pemutihan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PemutihanRequest extends FormRequest
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
            'submmission_date' => 'required',
            'target' => 'required',
            'pic' => 'required',
            'valued' => 'required',
            'location' => 'required',
            'clean_type' => 'required',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'submmission_date.required' => 'Tanggal Pemutihan wajib diisi.',
            'target.required' => 'Target Pemutihan wajib diisi.',
            'location.required' => 'Lokasi Pemutihan wajib diisi.',
            'clean_type.required' => 'Tipe Pemutihan wajib diisi.',
            'pic.required' => 'Penanggung Jawab Pemutihan wajib diisi.',
            'valued.required' => 'Pendapatan Pemutihan wajib diisi.',
        ];
    }
}
