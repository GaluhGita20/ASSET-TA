<?php

namespace App\Http\Requests\Inventaris;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class KibFRequest extends FormRequest
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
            'wide'=>'required',
            // 'wide_bld' => 'required',
            // 'land_status' => 'required',
            'tanah_id' => 'required',
            'coa_id' => 'required',
            'no_sertificate' => 'required',
            'sertificate_date' =>'required',
            'useful' => 'required',
            'residual_value' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'wide.required'=> 'Luas Lantai Wajib Diisi',
            'wide_bld.required' => 'Luas Bangunan Wajib Diisi.',
            // 'land_status.required' => 'Status Tanah Wajib Diisi.',
            'tanah_id.required' => 'Kode Aset Tanah Wajib Diisi',
            'coa_id.required' => 'Kode Aset Wajib Diisi',
            'no_sertificate.required' => 'Nomor Dokumen Wajib Diisi',
            'sertificate_date.required' => 'Tanggal Sertifikat Wajib Diisi',
            'useful.required' => 'Masa Manfaat Wajib Diisi.',
            'residual_value.required' => 'Nilai Residu Diisi',
        ];
    }
}
