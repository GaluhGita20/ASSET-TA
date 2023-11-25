<?php

namespace App\Http\Requests\Master\Location;

use App\Http\Requests\FormRequest;

// use Illuminate\Validation\Rule;
class LocationRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        //$id = ($this->record && property_exists($this->record, 'id')) ? $this->record->id : 0;

        $rules = [
     
            'space_code'  => 'required|unique:ref_location_aset,space_code,'.$id.',id',
            'name'      => 'required|max:255|unique:ref_locaton_aset,name,'.$id.',id',
            'floor_position' => 'required',
            'departemen_id' => 'required',
            'name' => 'required',
           
            'space_manager_id' => 'required',
        ];

        return $rules;
    }

}