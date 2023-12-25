<?php

namespace App\Http\Requests\Master\Location;

use App\Http\Requests\FormRequest;

// use Illuminate\Validation\Rule;
class LocationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        $id = $this->record->id ?? 0;
        $rules = [
            'name' => 'required|unique:ref_location_aset,name,'.$id.'$id',
           // 'name'      => 'required|unique:ref_location_aset,name,'.$id.'id',
            'space_code'  => 'required|unique:ref_location_aset,space_code,'.$id,
            'floor_position' => 'required',
            'departemen_id' => 'required',
            //'name' => 'required',
            'pic_id' => 'required',
        ];

        return $rules;
    }

}