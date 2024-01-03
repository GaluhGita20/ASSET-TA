<?php

namespace App\Http\Requests\Master\Org;

use App\Http\Requests\FormRequest;

class PositionRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        $loc = $this->location_id ?? 0;
        $rules = [
            'location_id' => 'required|exists:ref_org_structs,id',
            'level' =>'required',
            // 'level_id' => 'required|exists:ref_level_positions,id',
            'name'        => 'required|string|max:255|unique:ref_positions,name,'.$id.',id,location_id,'.$loc,
        ];

        return $rules;
    }

    public function messages(){
        return [
            'location_id.required' => 'Lokasi Harus Diisi.',
            'level.required' => 'Level Harus Diisi.',
            'name.unique' => 'Nama Jabtana Sudah Tersedia.',
        ];
    }
}