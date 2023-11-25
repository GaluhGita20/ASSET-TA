<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Foundation\Http\FormRequest;

class PembelianDetailRequest extends FormRequest
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
            'coa_id' => 'required',
            //'pembelian_id' =>'required',
            'existing_amount' => 'required',
            'requirement_standard' => 'required',
            'qty_req' => 'required',
        ];
        return $rules;

    }
}
