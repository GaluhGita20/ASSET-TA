<?php

namespace App\Http\Requests\Master\Org;

use App\Http\Requests\FormRequest;

class LevelPositionRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        
        $rules = [
            'name' => 'required|string|unique:ref_level_positions,name,'.$id,
            'description' => 'nullable|string',
        ];

        return $rules;
    }

    public function messages(){
        return [
            'name.required' => 'Nama Harus Diisi.',
            'name.unique' => 'Nama Sudah Tersedia.',
        ];
    }
}
