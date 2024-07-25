<?php

namespace App\Http\Requests\Master\Org;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class BodRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;

        $rules = [
            'parent_id' => [
                'required',
                // 'different:id',
                'exists:ref_org_structs,id'
            ],
        // 'code_manual'  => 'required|unique:ref_org_structs,code_manual,'.$id.',id',
        
            'name' => [
                'required',
                'max:255',
                Rule::unique('ref_org_structs')->where(function ($query) use ($id) {
                    return $query->where('parent_id', $this->parent_id)
                    ->where('id', '!=', $id);
                }),
            ],
        ];

                // 'code' =>[
                //     'required',
                //     'unique:ref_org_structs,code,'.$id.',id,level,bod'
                // ]
        return $rules;
    }

    public function messages()
    {
        return [
            'different' => 'zxc',
            'parent_id.required' => 'Nama Induk Perusahaan Harus Diisi',
            'parent_id.unique' => 'Nama Departemen Ini Sudah Tersedia',
            'name.required' => 'Nama Departemen Diperlukan',
            //'telegram_id.required' => 'Kode ID grup telegram harus diisi' 
        ];
    }
}
