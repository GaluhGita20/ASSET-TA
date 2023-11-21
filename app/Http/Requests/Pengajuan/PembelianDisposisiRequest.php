<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Foundation\Http\FormRequest;

class PembelianDisposisiRequest extends FormRequest
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
            'sentence_start' => 'required',
            'sentence_end' => 'required',
            'note_disposisi' => 'nullable',
        ];
        return $rules;

    }
}
