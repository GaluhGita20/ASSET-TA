<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PerubahanPerencanaanRequest extends FormRequest
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
            'usulan_id'=>['required',
                Rule::unique('trans_perubahan_usulan')->where(function ($query) {
                    return $query->where('usulan_id', request()->input('usulan_id'));
                })->ignore($id),
            ],
            'note' => 'required',
        ];

        
        return $rules;

    }

    public function messages()
    {
        //dd($errors->all());
        return [
            'note.required' => 'Catatan Penolakan Wjib Diisi.',
            'usulan_id.required' => 'Nama Aset Wajib Diisi',
            'usulan_id.unique' => 'Usulan Aset Ini Sudah Diajukan Perubahan.',
        ];
    }
}
