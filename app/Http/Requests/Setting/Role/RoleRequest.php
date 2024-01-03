<?php

namespace App\Http\Requests\Setting\Role;

use App\Http\Requests\FormRequest;

class RoleRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        return [
            'name' => 'required|string|max:255|unique:sys_roles,name,'.$id,
        ];
    }

    public function messages(){
        return [
            'name.required' => 'Nama Harus Diisi.',
            'name.unique' => 'Nama Role Sudah Digunakan.',
        ];
    } 
}
