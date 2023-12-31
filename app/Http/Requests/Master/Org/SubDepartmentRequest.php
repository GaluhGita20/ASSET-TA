<?php

namespace App\Http\Requests\Master\Org;

use App\Http\Requests\FormRequest;

class SubDepartmentRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        $rules = [
            'parent_id' => 'required|exists:ref_org_structs,id',
            //'code_manual'  => 'required|unique:ref_org_structs,code_manual,'.$id.',id',
            'name'      => 'required|string|max:255|unique:ref_org_structs,name,'.$id.',id,level,subdepartment',
        ];

        return $rules;
    }

    public function messages(){
        return [
            'parent_id.required' => 'Nama Parent Departemen Harus Diisi.',
            'name.required' => 'Nama Harus Diisi.',
        ];
    }
    
}