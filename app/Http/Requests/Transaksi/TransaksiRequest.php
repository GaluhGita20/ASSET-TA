<?php

namespace App\Http\Requests\Transaksi;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class TransaksiRequest extends FormRequest
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
            //'code' => 'required|string|unique:trans_usulan,code,'.$id,
            'trans_name' => 'required',
            'vendor_id' => 'required',
            'jenis_pengadaan_id' => 'required',
            'no_spk' => 'required',
            'spk_start_date' => 'required',
            'spk_end_date' => 'required',
            // 'procurement_year'=>['required',
            // 'required','integer','min:1900','max:2100',
            //     Rule::unique('trans_usulan')->where(function ($query) {
            //         return $query->where('struct_id', request()->input('struct_id'));
            //     })->ignore($id),
            // ],
            'budget_limit' => 'required',
            'qty' => 'required',
            'unit_cost' => 'required',
            'shiping_cost' => 'required',
            'tax_cost' => 'required',
            'total_cost' => 'required',
        ];

        
        return $rules;

    }

    public function messages()
    {
        return [
            'trans_name.required' => 'Nama Transaksi wajib diisi.',
            'ref_vendor.required' => 'Nama Vendor wajib diisi.',
            'ref_jenis_pengadaan.unique' => 'Jenis Pengadaan wajib diisi.',
            'no_spk.required' => 'Nomor Kontrak wajib diisi.',
            'spk_start_date.required' => 'Tanggal Mulai Kontrak wajib diisi.',
            'spk_end_date.required' => 'Tanggal Selesai Kontrak wajib diisi.',
            'budget_limit.required' => 'Pagu wajib diisi.',
            'qty.required' => 'Jumlah Pembelian wajib diisi.',
        ];
    }
}
