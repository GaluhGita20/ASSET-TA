<?php

namespace App\Http\Requests\Inventaris;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class KibARequest extends FormRequest
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
            'land_rights' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'address' => 'required',
            'coa_id' => 'required',
            'district_id' => 'required',
            'land_use' =>'required',
            'no_sertificate' =>'required',
            'sertificate_date' =>'required',

        ];

        
        return $rules;

    }

    public function messages()
    {
        return [
            'coa_id'=> 'Kode Aset Tanah Wajib Diisi',
            'wide.required' => 'Luas Tanah wajib Diisi.',
            'land_rights.required' => 'Hak Tanah wajib Diisi.',
            'province_id.required' => 'Provinsi Wajib Diisi',
            'city_id.required' => 'Kota wajib Diisi.',
            'district_id.required' => 'Daerah wajib Diisi.',
            'address.required' => 'Alamat wajib Diisi.',
            'land_use.required' => 'Kegunaan Tanah Wajib Diisi',
            'no_sertificate.required' => 'Nomor Sertifikat Harus Diisi',
            'sertificate_date.required' =>'Tanggal Sertifikat Harus Diisi',
        ];
    }
}
