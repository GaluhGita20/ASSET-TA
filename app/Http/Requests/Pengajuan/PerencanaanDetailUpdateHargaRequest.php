<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class PerencanaanDetailUpdateHargaRequest extends FormRequest
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
        $id = $this->detail->id ?? 0;
        $perencanaan = $this->perencanaan_id;
        $rules= [

            'HPS_unit_cost' => 'required',

            ];
        return $rules;

    }

    public function messages()
{
    return [
        'HPS_unit_cost.required' => 'Field HPS Harga Satuan harus diisi.',
    ];
}
}
