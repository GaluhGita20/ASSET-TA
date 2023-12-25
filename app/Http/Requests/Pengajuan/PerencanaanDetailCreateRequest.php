<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class PerencanaanDetailCreateRequest extends FormRequest
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
            'ref_aset_id' => [
                'required',
                Rule::unique('trans_usulan_details', 'ref_aset_id')
                    ->where('perencanaan_id', $perencanaan)->ignore($id,'id'),
            ],
            'desc_spesification' =>'required',
            'existing_amount' => 'required',
            'requirement_standard' => 'required',
            'qty_req' => 'required',
            'HPS_unit_cost' => 'required',
            'HPS_total_cost' =>'required',
            ];
        return $rules;

    }

    public function messages()
{
    return [
        'ref_aset_id.required' => 'Field Referensi Aset harus diisi.',
        'ref_aset_id.unique' => 'Referensi Aset sudah digunakan untuk perencanaan ini.',
        'desc_spesification.required' => 'Field Deskripsi Spesifikasi harus diisi.',
        'existing_amount.required' => 'Field Jumlah Yang Ada harus diisi.',
        'requirement_standard.required' => 'Field Standar Persyaratan harus diisi.',
        'qty_req.required' => 'Field Jumlah Yang Diminta harus diisi.',
        //'qty_req.numeric' => 'Field Jumlah Yang Diminta harus berupa angka.',
        'HPS_unit_cost.required' => 'Field HPS Harga Satuan harus diisi.',
        'HPS_total_cost.required' => 'Field HPS Total Biaya harus diisi.',
    ];
}
}
