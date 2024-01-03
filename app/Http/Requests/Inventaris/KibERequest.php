<?php

namespace App\Http\Requests\Inventaris;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class KibERequest extends FormRequest
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

        $rules= [
            'title'=>'required',
            // 'creator' => 'required',
            // 'size_animal' => 'required',
            // 'tipe_animal' => 'required',
            // 'material' => 'required',
            'coa_id' => 'required',
            'useful' => 'required',
            'residual_value' => 'required',
            'room_location' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required'=> 'Judul Wajib Diisi',
            // 'creator.required' => 'Pencipta Wajib Diisi.',
            'room_location.required' => 'Lokasi Ruang Wajib Diisi',
            // 'size_animal.required' => 'Ukuran Wajib Diisi.',
            // 'tipe_animal.required' => 'Status Tanah Wajib Diisi.',
            // 'material.required' => 'Bahan Wajib Diisi',
            'coa_id.required' => 'Kode Aset Wajib Diisi',
            'useful.required' => 'Masa Manfaat Wajib Diisi.',
            'residual_value.required' => 'Nilai Residu Diisi',
        ];
    }
}
