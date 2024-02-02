<?php

namespace App\Http\Requests\Transaksi;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class HibahDetailRequest extends FormRequest
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

        $trans = $this->trans_id;
        $rules= [
            'ref_aset_id' => [
                'required',
                // Rule::unique('trans_usulan_details', 'ref_aset_id')
                //     ->where('trans_id', $trans)->ignore($id,'id'),
            ],
            'desc_spesification' =>'required',
            'qty_agree' => 'required',
            'HPS_unit_cost' => 'required',
            ];
        return $rules;
    }

    public function messages()
    {
        return [
            'ref_aset_id.required' => 'Field Referensi Aset harus diisi.',
            'ref_aset_id.unique' => 'Referensi Aset sudah tersedia untuk penerimaan ini.',
            'desc_spesification.required' => 'Field Deskripsi Spesifikasi harus diisi.',
            'qty_agree.required' => 'Field Jumlah Diterima harus diisi.',
            'HPS_unit_cost.required' => 'Field Harga Unit Aset harus diisi.',
        ];
    }
}
