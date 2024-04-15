<?php

namespace App\Http\Requests\Master\StatusTanah;

use Illuminate\Foundation\Http\FormRequest;

class StatusTanahRequest extends FormRequest
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
            'name' => 'required|unique:ref_status_tanah,name,'.$id,
            //'jenis_pengadaan' => 'required|string|max:255|unique:ref_jenis_pengadaan,name,'.$id,
            'description' => 'required|string|max:255|',
        ];

        return $rules;
    }

    public function messages(){
        return [
            'name.required' => 'Nama Harus Diisi.',
            'description.required' => 'Deskripsi Harus Diisi.',
            'name.unique' => 'Status Tanah Ini Sudah Tersedia.',
        ];
    }

}
