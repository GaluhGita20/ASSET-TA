<?php

namespace App\Http\Requests\Master\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class TypeVendorRequest extends FormRequest
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
            //'id_vendor' => 'required|string|max:255|unique:ref_type_vendor,name,'.$id,
            'name' => 'required|string|unique:ref_type_vendor,name,'.$id,
            'description' => 'required',
        ];
         return $rules;
    }

    public function messages(){
        return [
            'name.required' => 'Nama Jenis Usaha Harus Diisi.',
            'name.unique' => 'Nama Jenis Usaha Sudah Digunakan.',
            'description.required' => 'Deskripsi Harus Diisi.',
        ];
    } 

}
