<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class PerencanaanDetailRequest extends FormRequest
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
        $perencanaan = $this->perencanaan_id;
        $rules= [
            'qty_agree' =>['required','lte:qty_req'],
            'HPS_total_agree' =>'required',
        //    'sumber_biaya_id' =>'required',
            ];
        return $rules;

    }

    public function messages()
{
    return [
      //  'ref_aset_id.required' => 'Field Referensi Aset harus diisi.',
        //'ref_aset_id.unique' => 'Referensi Aset sudah digunakan untuk perencanaan ini.',
        //'desc_spesification.required' => 'Field Deskripsi Spesifikasi harus diisi.',
        //'existing_amount.required' => 'Field Jumlah Yang Ada harus diisi.',
        //'requirement_standard.required' => 'Field Standar Persyaratan harus diisi.',
        //'qty_req.required' => 'Field Jumlah Yang Diminta harus diisi.',
        //'qty_req.numeric' => 'Field Jumlah Yang Diminta harus berupa angka.',
       // 'HPS_unit_cost.required' => 'Field HPS Harga Satuan harus diisi.',
        //'HPS_total_cost.required' => 'Field HPS Total Biaya harus diisi.',
        'qty_agree.required' => 'Field Jumlah Disetujui harus diisi.',
        'qty_agree.numeric' => 'Field Jumlah Disetujui harus berupa angka.',
        'qty_agree.lte' => 'Field Jumlah Disetujui harus lebih kecil atau sama dengan Jumlah Yang Diminta.',
        'HPS_total_agree.required' => 'Field HPS Total Disetujui harus diisi.',
       // 'ref_sumber_biaya.required' => 'Field Referensi Sumber Biaya harus diisi.',
    ];
}
}
