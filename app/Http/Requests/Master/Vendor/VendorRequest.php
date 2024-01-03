<?php

namespace App\Http\Requests\Master\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
            //'id_vendor' => 'required|string|max:255|unique:ref_vendor,name,'.$id,
            'name' => 'required|string|unique:ref_vendor,name,'.$id,
            'address' => 'required',
            'telp' => 'required',
            'leader' =>'required',
            'jenisUsaha' => 'required',
            'email' => 'required',
            // 'kode_rekening' => 'required',
            //'instansi_code' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' =>'required',
            'contact_person' => 'required',

            // 'status' => 'required',
        ];
        return $rules;

    }

    public function messages(){
        return [
            'name.required' => 'Nama Vendor Harus Diisi.',
            'name.unique' => 'Nama Vendor Sudah Digunakan.',
            'address.required' => 'Lokasi Alamat Harus Diisi.',

            'leader.required' => 'Pimpinan Harus Diisi.',
            'telp.required' => 'Nomor Telpon Vendor Harus Diisi.',
            'jenisUsaha.required' => 'Jenis Usaha Harus Diisi.',
            'email.required' => 'Email Harus Diisi.',
            'contact_pereson.required' => 'Nomor Kontak Person Harus Diisi.',
            'province_id.required' => 'Lokasi Provinsi Harus Diisi.',
            'city_id.required' => 'Lokasi Kota Harus Diisi.',
            'district_id.required' => 'Lokasi Daerah Harus Diisi.',
        ];
    }

}
