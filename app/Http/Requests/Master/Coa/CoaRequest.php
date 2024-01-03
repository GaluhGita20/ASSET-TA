<?php

namespace App\Http\Requests\Master\Coa;

use Illuminate\Foundation\Http\FormRequest;

class CoaRequest extends FormRequest
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
        $rules = [
            'kode_akun' => 'required|unique:ref_coa,kode_akun,'.$id,
            'nama_akun' => 'required|string|max:255|unique:ref_coa,nama_akun,'.$id,
            'tipe_akun' => 'required',
        ];

        return $rules;
    }

    public function messages(){
        return [
            'kode_akun.required' => 'Kode Akun Harus Diisi.',
            'nama_akun.required' => 'Nama Akun Harus Diisi.',
            'tipe_akun.required' => 'Tipe Akun Harus Diisi.',
            'kode_akun.unique' => 'Kode Akun Ini Sudah Tersedia.',
            'nama_akun.unique' => 'Nama Akun Ini Sudah Tersedia.',
        ];
    }

}
