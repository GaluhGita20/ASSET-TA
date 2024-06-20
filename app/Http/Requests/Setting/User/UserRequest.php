<?php

namespace App\Http\Requests\Setting\User;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->record->id ?? 0;
        $rules = [
            'name'        => 'required|string|max:255',
            'nip'        => 'required|numeric|unique:sys_users,nip,' . $id,
            'username'    => 'required|unique:sys_users,username,' . $id,
            'email'       => 'required|string|max:60|email|unique:sys_users,email,' . $id,
            'location_id' => 'required',
            'position_id' => 'required|exists:ref_positions,id',
            'status'      => 'required',
        ];
        if ($id == 1) {
            unset($rules['position_id']);
            unset($rules['nip']);
        }
        
        if (!$id) {
            $password_rules = [
                function ($attribute, $value, $fail) {
                    $str = ':attribute harus: ';
                    $msg = [];
                    if (strlen($value) < 8) {
                        $msg []= 'min 8 karakter';
                    }
                    if (!preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
                        $msg []= 'berisi setidaknya satu huruf besar dan satu huruf kecil';
                    }
                    if (!preg_match('/\pL/u', $value)) {
                        // $fail('harus berisi setidaknya satu huruf.');
                    }
                    // if (!preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)) {
                    //     $fail('harus mengandung setidaknya satu simbol.');
                    // }
                    if (!preg_match('/\pN/u', $value)) {
                        $msg []= 'mengandung setidaknya satu angka';
                    }
                    if (count($msg)) {
                        $fail($str.''.implode(', ', $msg));
                    }
                },
            ];
            $rules += [
                'username'              => 'required|string|max:60|unique:sys_users,username,' . $id,
                'password'              => [
                    'required',
                    'confirmed',
                    // ...$password_rules,
                ],
                'password_confirmation' => [
                    'required',
                    // ...$password_rules,
                ],
            ];
        }
        return $rules;
    }

    public function messages()
    {
        //dd($errors->all());
        return [
            'name.required' => 'Nama Wjib Diisi.',
            'nip.required' => 'NIP Wajib Diisi',
            'username.required' => 'Username Wajib Diisi',
            'email.required' => 'Email Wajib Diisi',
            'username.unique' => 'Username Ini Sudah Tersedia',
            'nip.unique' => 'NIP Ini Sudah Tersedia',
            'email.unique' => 'Email Ini Sudah Tersedia',
        ];
    }
}
