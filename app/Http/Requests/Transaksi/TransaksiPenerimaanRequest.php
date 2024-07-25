<?php

namespace App\Http\Requests\Transaksi;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class TransaksiPenerimaanRequest extends FormRequest
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
            'trans_name' => 'required',
            'vendor_id' => 'required',
            'jenis_pengadaan_id' => 'required',
            'no_spk' => 'required',
            'spk_start_date' => 'required',
            'spk_end_date' => 'required',
            // 'budget_limit' => 'required',
            // 'qty' => 'required',
            // 'unit_cost' => 'required',
            'shiping_cost' => 'required',
            'tax_cost' => 'required',
            'total_cost' => 'required',
            'receipt_date'=> 'required',
            'faktur_code' => 'required',
            'location_receipt' => 'required',
            'spm_date'=> 'required',
            'spm_code' => 'required',
            // 'sp2d_code'=> 'required',
            // 'sp2d_date' => 'required',
            // 'asset_test_results' => 'required',
            'user_id' => 'required',
        ];

        
        return $rules;

    }

    public function messages()
    {
        return [
            'trans_name.required' => 'Nama Transaksi wajib diisi.',
            'vendor_id.required' => 'Nama Vendor wajib diisi.',
            'jenis_pengadaan_id.required' => 'Jenis Pengadaan wajib diisi.',
            'no_spk.required' => 'Nomor Kontrak wajib diisi.',
            'spk_start_date.required' => 'Tanggal Mulai Kontrak wajib diisi.',
            'spk_end_date.required' => 'Tanggal Selesai Kontrak wajib diisi.',
            // 'budget_limit.required' => 'Pagu wajib diisi.',
            // 'qty.required' => 'Jumlah Pembelian wajib diisi.',

            'receipt_date.required' => 'Tanggal Terima wajib diisi.' ,
            'faktur_code.required' => 'Kode Faktur wajib diisi.',
            'spm_date.required' => 'Tanggal SPM (Surat Perintah Pembayaran) wajib diisi.' ,
            'spm_code.required' => 'Kode SPM (Surat Perintah Pembayaran) wajib diisi.',
            'location_receipt.required' => 'Lokasi Penerimaan wajib diisi.',
            // 'sp2d_code.required' => 'Kode SP2D wajib diisi.',
            // 'sp2d_date.required' => 'Tanggal SP2D wajib diisi.',
            // 'asset_test_results.required' => 'Hasil Test wajib diisi.',
            'user_id.required' => 'User Penguji wajib diisi.',
        ];
    }
}
