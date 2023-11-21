<?php

namespace Database\Seeders;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Administrator',
            ]
        );

        $ROLES = [
            [
                'name'  => 'Administrator',
            ],
            [
                'name'  => 'Direksi',
            ],
            [
                'name'  => 'Sub Bagian Program Perencanaan', // sub departemen
            ],
            [
                'name'  => 'Keuangan',
            ],
            [
                'name'  => 'Umum',
            ],
            [
                'name'  => 'Sarpras',
            ],
            [
                'name'  => 'BPKAD',
            ],
        ];

        // foreach ($ROLES as $value) {
        //     $record = Role::firstOrNew(['name'  => $value['name']]);
        //     if (!$record->id) {
        //         // $record->id            = $value['id'];
        //         $record->name           = $value['name'];
        //         $record->model_type     = 'App\Models\Auth\User';

        //         $record->save();
        //     }
        // }

        // $password = bcrypt('password');
        // $user = User::firstOrCreate(
        //     ['id' => 1],
        //     [
        //         'name' => 'Administrator',
        //         'username' => 'admin',
        //         'email' => 'admin@email.com',
        //         'password' => $password,
        //         'nip' => null,
        //         'position_id' => null,
        //     ],
            // ['id' => 2],
            // [// Direktur
            //     'name' => 'drg. I Made Suasa',
            //     'username' => 'suasa',
            //     'email' => 'suasa@email.com',
            //     'password' => $password,
            //     'nip' => '1971517171717171',
            //     'position_id' => 1,
            // ],
            // ['id' => 3],
            // [ //kabag TU
            //     'name' => 'Dharma Cakrawarthana Sutra, SKM.MPH',
            //     'username' => 'dharma',
            //     'email' => 'dharma@email.com',
            //     'password' => $password,
            //     'nip' => '1972517171717171',
            //     'position_id' => 6,
            // ],
            // ['id' => 4],
            // [// SUBAG Perencanaan
            //     'name' => 'Hengki Zulya Primadi, S.Kep.Ns',
            //     'username' => 'hengki',
            //     'email' => 'hengki@email.com',
            //     'password' => $password,
            //     'nip' => '1973517171717171',
            //     'position_id' => 11,
            // ],
            // ['id' => 5],
            // [ //SUBAG Keuangan
            //     'name' => 'Sumardi, SE',
            //     'username' => 'sumardi',
            //     'email' => 'sumardi@email.com',
            //     'password' => $password,
            //     'nip' => '1974517171717171',
            //     'position_id' => 12,
            // ],
            // ['id' => 6],
            // [// SUBAG Umum Kepegawaian
            //     'name' => 'Harry Kuswandi Ardhi S.Kep.Ns',
            //     'username' => 'harry',
            //     'email' => 'harry@email.com',
            //     'password' => $password,
            //     'nip' => '1975517171717171',
            //     'position_id' => 20,
            // ],
            // ['id' => 7],
            // [ //KABID Pelayanan Medik Keperawatan
            //     'name' => 'dr. H Encu Sukandi, sp.MK ',
            //     'username' => 'encu',
            //     'email' => 'encu@email.com',
            //     'password' => $password,
            //     'nip' => '1976517171717171',
            //     'position_id' => 4,
            // ],
            // ['id' => 8],
            // [// Seksi Pelayanan Medik
            //     'name' => 'dr. Dian',
            //     'username' => 'dian',
            //     'email' => 'dian@email.com',
            //     'password' => $password,
            //     'nip' => '1977517171717171',
            //     'position_id' => 9,
            // ],
            // ['id' => 9],
            // [ //Seksi Pelayanan Keperawatan
            //     'name' => 'Endang Puji Astusti, S.Kep ',
            //     'username' => 'puji',
            //     'email' => 'puji@email.com',
            //     'password' => $password,
            //     'nip' => '1978517171717171',
            //     'position_id' => 10,
            // ] ,['id' => 10],
            // [ //KABID Penunjang
            //     'name' => 'I Gede Putu Arta , S.Kep ',
            //     'username' => 'arta',
            //     'email' => 'arta@email.com',
            //     'password' => $password,
            //     'nip' => '1979517171717171',
            //     'position_id' => 3,
            // ],['id' => 11],
            // [// Seksi Penunjang Medik dan Non Medik
            //     'name' => 'Sapurni',
            //     'username' => 'sapurni',
            //     'email' => 'sapurni@email.com',
            //     'password' => $password,
            //     'nip' => '1980517171717171',
            //     'position_id' => 7,
            // ],
            // ['id' => 12],
            // [ //kasie Sarana Prasarana Logistik
            //     'name' => 'Ahmad, SKM ',
            //     'username' => 'ahmad',
            //     'email' => 'ahmad@email.com',
            //     'password' => $password,
            //     'nip' => '1981517171717171',
            //     'position_id' => 8,
            // ],
            // ['id' => 13],
            // [ //KABID PSDM
            //     'name' => 'Ni Nengah Winarni, S.Keb ',
            //     'username' => 'winarni',
            //     'email' => 'winarni@email.com',
            //     'password' => $password,
            //     'nip' => '1982517171717171',
            //     'position_id' => 5,
            // ],
            // ['id' => 14],
            // [// Seksi PSDM & Diklat
            //     'name' => 'Okta Santika Iriana, Skep.Ns',
            //     'username' => 'okta',
            //     'email' => 'okta@email.com',
            //     'password' => $password,
            //     'nip' => '1983517171717171',
            //     'position_id' => 32, 
            // ],
            // ['id' => 15],
            // [ //Seksi Humas
            //     'name' => 'Anggi Hidayat, S.Kep.Ns',
            //     'username' => 'anggi',
            //     'email' => 'anggi@email.com',
            //     'password' => $password,
            //     'nip' => '1984517171717171',
            //     'position_id' => 33,
            // ],
            // ['id' => 16],
            // [ //Kepala IGD
            //     'name' => 'dr. Ahmad Haerul Umam',
            //     'username' => 'umam',
            //     'email' => 'umam@email.com',
            //     'password' => $password,
            //     'nip' => '1985517171717171',
            //     'position_id' => 29,
            // ],['id' => 17],
            // [ //Staf IGD
            //     'name' => 'Sri Adriani, Amd.Keb',
            //     'username' => 'sri',
            //     'email' => 'sri@email.com',
            //     'password' => $password,
            //     'nip' => '1986517171717171',
            //     'position_id' => 31, 
            // ],['id' => 18],
            // [ //Kepala IPSRS
            //     'name' => 'Ahmad Haerul',
            //     'username' => 'haerul',
            //     'email' => 'haerul@email.com',
            //     'password' => $password,
            //     'nip' => '1987517171717171',
            //     'position_id' => 28,
            // ]
        // );

        // $user->assignRole($role);

        // foreach ($user as $key => $val){
        //     $data = User::where('id', $val['id'])->first();
        //    // $kab = City::where('code', $val->id)->first();

        //     if (!$data) {
        //         $data = new User;
        //         $data->id = $val['id'];
        //         $data->name = $prov['id'];
        //         $data->nip = $val['id'];
        //         $data->username = $val['username'];
        //         $record->status  = $val['status'];
        //         $record->password  = $val['password'];
        //         $record->position_id = $val['position_id'];
        //         $data->created_by = 1;
        //         $data->created_at = \Carbon\Carbon::now();
        //         $data->save();
        //         $record->roles()->sync($value['role_ids']);
        //     }
        // }

        foreach ($ROLES as $value) {
            $record = Role::firstOrNew(['name'  => $value['name']]);
            $record->save();
        }

        $password = bcrypt('password');
        $user = User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@email.com',
                'password' => $password,
                'nik' => 'admin',
            ]
        );
        $user->assignRole($role);


        $USERS = [
            // ['id' => 1],
            // [
            //     'name' => 'Administrator',
            //     'username' => 'admin',
            //     'email' => 'admin@email.com',
            //     'password' => $password,
            //     'nip' => null,
            //     'position_id' => null,
            // ],
    
            [// Direktur
                'name' => 'drg. I Made Suasa',
                'username' => 'suasa',
                'email' => 'suasa@email.com',
                'password' => $password,
                'nip' => '1971517171717171',
                'position_id' => 1,
                'role_ids' =>[2], 
            ],
      
            [ //kabag TU
                'name' => 'Dharma Cakrawarthana Sutra, SKM.MPH',
                'username' => 'dharma',
                'email' => 'dharma@email.com',
                'password' => $password,
                'nip' => '1972517171717171',
                'position_id' => 6,
                'role_ids' =>[1], 

            ],
   
            [// SUBAG Perencanaan
                'name' => 'Hengki Zulya Primadi, S.Kep.Ns',
                'username' => 'hengki',
                'email' => 'hengki@email.com',
                'password' => $password,
                'nip' => '1973517171717171',
                'position_id' => 11,
                'role_ids' =>[3], 
            ],
   
            [ //SUBAG Keuangan
                'name' => 'Sumardi, SE',
                'username' => 'sumardi',
                'email' => 'sumardi@email.com',
                'password' => $password,
                'nip' => '1974517171717171',
                'position_id' => 12,
                'role_ids' =>[4], 
            ],
      
            [// SUBAG Umum Kepegawaian
                'name' => 'Harry Kuswandi Ardhi S.Kep.Ns',
                'username' => 'harry',
                'email' => 'harry@email.com',
                'password' => $password,
                'nip' => '1975517171717171',
                'position_id' => 20,
                'role_ids' =>[1], 
            ],
     
            [ //KABID Pelayanan Medik Keperawatan
                'name' => 'dr. H Encu Sukandi, sp.MK ',
                'username' => 'encu',
                'email' => 'encu@email.com',
                'password' => $password,
                'nip' => '1976517171717171',
                'position_id' => 4,
                'role_ids' =>[5], 
            ],
       
            [// Seksi Pelayanan Medik
                'name' => 'dr. Dian',
                'username' => 'dian',
                'email' => 'dian@email.com',
                'password' => $password,
                'nip' => '1977517171717171',
                'position_id' => 9,
                'role_ids' =>[5], 
            ],
      
            [ //Seksi Pelayanan Keperawatan
                'name' => 'Endang Puji Astusti, S.Kep ',
                'username' => 'puji',
                'email' => 'puji@email.com',
                'password' => $password,
                'nip' => '1978517171717171',
                'position_id' => 10,
                'role_ids' =>[5], 
            ] ,
            [ //KABID Penunjang
                'name' => 'I Gede Putu Arta , S.Kep ',
                'username' => 'arta',
                'email' => 'arta@email.com',
                'password' => $password,
                'nip' => '1979517171717171',
                'position_id' => 3,
                'role_ids' =>[5], 
            ],
            [// Seksi Penunjang Medik dan Non Medik
                'name' => 'Sapurni',
                'username' => 'sapurni',
                'email' => 'sapurni@email.com',
                'password' => $password,
                'nip' => '1980517171717171',
                'position_id' => 7,
                'role_ids' =>[5], 
            ],
   
            [ //kasie Sarana Prasarana Logistik
                'name' => 'Ahmad, SKM ',
                'username' => 'ahmad',
                'email' => 'ahmad@email.com',
                'password' => $password,
                'nip' => '1981517171717171',
                'position_id' => 8,
                'role_ids' =>[6], 
            ],
  
            [ //KABID PSDM
                'name' => 'Ni Nengah Winarni, S.Keb ',
                'username' => 'winarni',
                'email' => 'winarni@email.com',
                'password' => $password,
                'nip' => '1982517171717171',
                'position_id' => 5,
                'role_ids' =>[5], 
            ],
       
            [// Seksi PSDM & Diklat
                'name' => 'Okta Santika Iriana, Skep.Ns',
                'username' => 'okta',
                'email' => 'okta@email.com',
                'password' => $password,
                'nip' => '1983517171717171',
                'position_id' => 32, 
                'role_ids' =>[5], 
            ],
   
            [ //Seksi Humas
                'name' => 'Anggi Hidayat, S.Kep.Ns',
                'username' => 'anggi',
                'email' => 'anggi@email.com',
                'password' => $password,
                'nip' => '1984517171717171',
                'position_id' => 33,
                'role_ids' =>[5], 
            ],
           
            [ //Kepala IGD
                'name' => 'dr. Ahmad Haerul Umam',
                'username' => 'umam',
                'email' => 'umam@email.com',
                'password' => $password,
                'nip' => '1985517171717171',
                'position_id' => 29,
                'role_ids' =>[5], 
            ],
            [ //Staf IGD
                'name' => 'Sri Adriani, Amd.Keb',
                'username' => 'sri',
                'email' => 'sri@email.com',
                'password' => $password,
                'nip' => '1986517171717171',
                'position_id' => 31, 
                'role_ids' =>[5], 
            ],

            [ //Kepala IPSRS
                'name' => 'Ahmad Haerul',
                'username' => 'haerul',
                'email' => 'haerul@email.com',
                'password' => $password,
                'nip' => '1987517171717171',
                'position_id' => 28,
                'role_ids' =>[6], 
            ]
        ];

        foreach ($USERS as $key => $value) {
            $record = User::firstOrNew(['username' => $value['username']]);
            if (!$record->id) {
                // $record->id            = $value['id'];
                $record->nip           = $value['nip'];
                $record->position_id    = $value['position_id'];
                $record->email          = $value['email'];
                $record->password       = $value['password'];
            }

            $record->username       = $value['username'];
            $record->name           = $value['name'];
            // $record->status         = $value['status'];
            $record->save();
            $record->roles()->sync($value['role_ids']);
        }

        // foreach ($user as $val) {
        
    }
}
