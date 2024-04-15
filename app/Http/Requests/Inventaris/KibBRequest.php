<?php

namespace App\Http\Requests\Inventaris;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class KibBRequest extends FormRequest
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
            'material'=>'required',
            'merek_type_item' => 'required',
            'useful' => 'required',
            'residual_value' => 'required',
        ];
        
        return $rules;
    }

    public function messages()
    {
        return [
            'material.required'=> 'Bahan Wajib Diisi',
            'merek_type_item.required' => 'Merek Tipe Barang Diisi.',
            'useful.required' => 'Masa Manfaat Wajib Diisi.',
            'residual_value.required' => 'Nilai Residu Diisi',
        ];
    }
}
