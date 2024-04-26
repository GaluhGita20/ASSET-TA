<?php

namespace App\Http\Requests\Master\Coa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        if($this->module == 'master_coa_tanah'){
            $type = 'KIB A';
        }elseif($this->module == 'master_coa_peralatan'){
            $type = 'KIB B';
        }elseif($this->module == 'master_coa_bangunan'){
            $type = 'KIB C';
        }elseif($this->module == 'master_coa_jalan_irigasi'){
            $type = 'KIB D';
        }elseif($this->module == 'master_coa_aset_lainya'){
            $type = 'KIB E';
        }elseif($this->module == 'master_coa_kontruksi_pembangunan'){
            $type = 'KIB F';
        }
        // dd($this->module);
        $rules = [
            'kode_akun' => 'required|unique:ref_kode_aset_bmd,kode_akun,' . $id,
            'nama_akun' => [
                'required',
                'max:255',
                Rule::unique('ref_kode_aset_bmd', 'nama_akun')->where(function ($query) use ($id, $type) {
                    return $query->where(function ($query) use ($id, $type) {
                        $query->where('tipe_akun', $this->tipe_akun)
                            ->where('id', '!=', $id);
                    })
                    ->orWhere(function ($query) use ($type, $id) {
                        $query->where('tipe_akun', $type)
                            ->where('id', '!=', $id);
                    });
                }),
            ],
        ];

        return $rules;
    }

    public function messages(){
        return [
            'kode_akun.required' => 'Kode Akun Harus Diisi.',
            'nama_akun.required' => 'Nama Akun Harus Diisi.',
            // 'tipe_akun.required' => 'Tipe Akun Harus Diisi.',
            'kode_akun.unique' => 'Kode Akun Ini Sudah Tersedia.',
            'nama_akun.unique' => 'Nama Akun Ini Sudah Tersedia.',
        ];
    }

}
