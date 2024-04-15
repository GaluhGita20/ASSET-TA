<?php

namespace App\Http\Requests\Perbaikan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UsulanSperpatRequest extends FormRequest
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
        // $perbaikan = $this->trans_perbaikan_id;
        $rules= [
            'sperpat_name'=>'required',
            'desc_sper' => 'required',
            'qty' => 'required',
            'unit_cost' => 'required',
            'total_cost' =>'required',
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            'sperpat_name.required' => 'Nama Sperpat perbaikan wajib diisi.',
            'desc_sper.required' => 'Detail Sperpat wajib diisi.',
            'qty.required' => 'Vendor wajib diisi.',
            'unit_cost.required' => 'Harga Unit Wajib diisi.',
            'total_cost.required' => 'Total Biaya harus diisi.',
        ];
    }
}
