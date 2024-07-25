<?php

namespace App\Http\Requests\Master\Org;

use App\Http\Requests\FormRequest;

class DepartmentRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        $rules = [
            'parent_id' => 'required|exists:ref_org_structs,id',
            //'code_manual'  => 'required|unique:ref_org_structs,code_manual,'.$id.',id',
            'name'      => 'required|string|max:255|unique:ref_org_structs,name,'.$id.',id,level,department',
           // 'grup_telegram' => 'required'
        ];

        return $rules;
    }

    public function messages(){
        return [
            'name.required' => 'Unit Departemen Harus Diisi.',
            'parent_id.required' => 'Induk Unit Departemen Harus Diisi.',
            'name.unique' => 'Unit Departemen Sudah Tersedia.',
           // 'grup_telegram.required' => 'Kode ID grup telegram harus diisi' 
        ];
    }
}