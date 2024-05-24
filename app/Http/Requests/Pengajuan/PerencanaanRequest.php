<?php

namespace App\Http\Requests\Pengajuan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class PerencanaanRequest extends FormRequest
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
          //  'code' => 'required|string|unique:trans_usulan,code,'.$id,
        //    'date' => 'required',
            'procurement_year'=>['required',
            'required','integer','min:1900','max:2100',
                Rule::unique('trans_usulan')->where(function ($query) {
                    return $query->where('struct_id', request()->input('struct_id'));
                })->ignore($id),
            ],
            // 'is_repair' => 'required',
            'struct_id' => 'required',
            'regarding' => 'required',
        ];

        
        return $rules;

    }

    public function messages()
    {
        //dd($errors->all());
        return [
            'procurement_year.required' => 'Periode pengadaan wajib diisi.',
            'procurement_year.integer' => 'Tahun pengadaan harus berupa angka.',
            'procurement_year.min' => 'Tahun pengadaan minimal 1900.',
            'procurement_year.max' => 'Tahun pengadaan maksimal 2100.',
            'procurement_year.unique' => 'Periode pengadaan sudah digunakan untuk struktur ini.',

            // 'is_repair.required' => 'Isian perbaikan wajib diisi.',
            'struct_id.required' => 'ID struktur wajib diisi.',
            'regarding.required' => 'Perihal wajib diisi.',
        ];
    }
}
