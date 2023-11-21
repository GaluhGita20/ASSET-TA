<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Foundation\Http\FormRequest;

class PembelianRequest extends FormRequest
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
            'code' => 'required|string|unique:trans_pengajuan_pembelian,code,'.$id,
            'date' => 'required',
            'struct_id' => 'required',
            'regarding' => 'required',
        ];
        return $rules;

    }
}
