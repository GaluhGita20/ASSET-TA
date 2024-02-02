<?php

namespace App\Http\Requests\Transaksi;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class HibahAsetRequest extends FormRequest
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
            //'code' => 'required|string|unique:trans_usulan,code,'.$id,
            'trans_name' => 'required',
            'vendor_id' => 'required',
            'receipt_date' => 'required',
        ];

        return $rules;

    }

    public function messages()
    {
        return [
            'trans_name.required' => 'Nama Transaksi wajib diisi.',
            'vendor_id.required' => 'Nama Vendor wajib diisi.',
            'receipt_date.required' => 'Tanggal Penerimaan wajib diisi.',
        ];
    }
}
