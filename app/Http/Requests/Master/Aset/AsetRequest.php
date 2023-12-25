<?php

namespace App\Http\Requests\Master\Aset;

use Illuminate\Foundation\Http\FormRequest;

class AsetRequest extends FormRequest
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
            'name' => 'required|unique:ref_aset,name,'.$id,
            //'jenis_pengadaan' => 'required|string|max:255|unique:ref_jenis_pengadaan,name,'.$id,
            'jenis_aset' => 'required|',
        ];

        return $rules;
    }

}
