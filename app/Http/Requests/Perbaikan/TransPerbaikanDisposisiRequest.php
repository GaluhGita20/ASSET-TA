<?php

namespace App\Http\Requests\Perbaikan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class TransPerbaikanDisposisiRequest extends FormRequest
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
            // 'perbaikan_id'=>'required',
            // 'repair_type' => 'required',
            // 'vendor_id' => 'required',
            'spk_start_date' => 'required',
            'spk_end_date' => 'required',
            'no_spk' => 'required',
            'faktur_code' => 'required',
            'receipt_date' => 'required',
            'shiping_cost' => 'required',
            'tax_cost' => 'required',

        ];

        return $rules;

    }

    public function messages()
    {
        return [
            // 'kib_id.required' => 'Nama Aset Wajib Diisi.',
            // 'perbaikan_id.required' => 'code perbaikan wajib diisi.',
            // 'repair_type.required' => 'Jenis Perbaikan wajib diisi.',
            // 'vendor.required' => 'Vendor wajib diisi.',

            'spk_start_date.required' => 'tanggal mulai kontrak wajib diisi.',
            'spk_end_date.required' => 'tanggal selesai kontrak wajib diisi.',
            'no_spk.required' => 'nomor kontrak wajib diisi.',
            'faktur_code.required' => 'kode faktur wajib diisi.',
            'receipt_date.required' => 'tanggal penerimaan wajib diisi.',
            'shiping_cost.required'=> 'biaya pengiriman wajib diisi.',
            'tax_cost.required' => 'biaya pajak wajib diisi.',
        ];
    }
}
