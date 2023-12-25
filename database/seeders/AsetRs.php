<?php

namespace Database\Seeders;
use App\Models\Master\Aset\Aset;
use File;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File as FacadesFile;

class AsetRs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        {
            $path = base_path('database/seeders/json/aset.json');
            $json = FacadesFile::get($path);
            $data = json_decode($json);
            $this->generate($data);
        }
    }

    public function generate($data)
    {
        foreach ($data as $val) {
            $aset = Aset::where('name', $val->nama)->first();

            if (!$aset && $val->nama){
                $aset = new Aset;
                $aset->name = $val->nama;
                $aset->jenis_aset = 'Peralatan Mesin';
                $aset->created_by = 1;
                $aset->created_at = \Carbon\Carbon::now();
                $aset->save();
            }
        }
    }
    
}
