<?php

namespace Database\Seeders;

use App\Models\Master\Coa\COA;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File as FacadesFile;

class CoaKPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path('database/seeders/json/coaKP.json');
        $json = FacadesFile::get($path);
        $data = json_decode($json);

        $this->generate($data);
    }

    public function generate($data)
    {
        foreach ($data as $val) {
            $coa = COA::where('kode_akun', $val->kode)->first();

            if (!$coa){
                $coa = new COA;
                $coa->kode_akun = $val->kode;
                $coa->nama_akun = $val->nama;
                $coa->tipe_akun = 'KIB F';
                $coa->created_by = 1;
                $coa->created_at = \Carbon\Carbon::now();
                $coa->save();
            }
        }
    }
}
