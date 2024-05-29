<?php

namespace App\Exports\Setting;

use App\Models\Inventaris\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KibAExport implements FromCollection, WithStyles
{
    public function thead()
    {
        return [
            'A' => 'No',
            'B' => 'Nama Aset',
            'C' => 'Kode Akun/Nama Akun',
            'D' => 'Nomor Register',
            'E' => 'Tanggal Register',
            'F' => 'Status',
            'G'=>'Luas (m2)',
            'H'=>'Provinsi',
            'I'=>'Kabupaten',
            'J'=>'Daerah',
            'K'=>'Alamat',
            'L' => 'Sumber Perolehan',
            'M' => 'Asal Usul',
            'N'=>'Hak Tanah',
            'O'=>'Nomor Sertifikat',
            'P'=>'Tanggal Sertifikat',
            'Q' => 'Kegunaan Tanah',
            'R' => 'Tahun Perolehan',
            'S' => 'Harga Perolehan (Rupiah)',
            'T' => 'Nilai Buku (Rupiah)',
            'U' => 'Akumulasi Kenaikan Harga (Rupiah)',
            'V' => 'Unit Pengusul',
            'W' => 'Keterangan',
        ];
    }

    public function collection()
    {
        $data = [];
        $data[] = array_values($this->thead());
        $records = Aset::where('type','KIB A')->grid()->filters()->get();
        foreach ($records as $i => $record) {
            $data[] = [
                ($i + 1),
                $record->usulans ? $record->usulans->asetd->name : '-',
                $record->coad ? $record->coad->kode_akun.'|'.$record->coad->nama_akun : '-',
                $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-',
                $record->book_date ? $record->book_date : '-',
                $record->status ? ($record->status == 'actives' ? ucfirst('active') : ($record->status == 'notactive' ? ucfirst($record->status) : ($record->status == 'in repair' ? ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))):'-',
                $record->wide ? number_format($record->wide, 0, ',', ','): '-',
                $record->province_id ? $record->provinsi->name : '-',
                $record->city_id ? $record->city->name : '-',
                $record->district_id ? $record->district->name : '-',
                $record->address ? ucwords($record->address) : '-',
                $record->usulans->danad ? $record->usulans->danad->name : '-',  
                $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ? ucfirst($record->usulans->trans->source_acq) : ucfirst($record->usulans->trans->source_acq),               
                $record->hakTanah->name ? $record->hakTanah->name : '-',
                $record->no_sertificate ? $record->no_sertificate : '-',
                $record->sertificate_date ? Carbon::parse($record->sertificate_date)->formatLocalized('%d/%B/%Y') : '-',
                $record->land_use ? ucwords($record->land_use) : '-',
                $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y'),  
                $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ','),          
                $record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-', 
                $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                !empty($record->usulans->perencanaan->struct) ? $record->usulans->perencanaan->struct->name : ($record->location_hibah_aset ? $record->deps->name : '-'),
                $record->description ? $record->description : '-',
            ];
        }

        return collect($data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(80);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(80);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(80);
        $sheet->getColumnDimension('O')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(30);
        $sheet->getColumnDimension('S')->setWidth(10);
        $sheet->getColumnDimension('T')->setWidth(30);
        $sheet->getColumnDimension('U')->setWidth(80);
        $sheet->getColumnDimension('V')->setWidth(30);
        $sheet->getColumnDimension('W')->setWidth(30);
        $sheet->getStyle('A1:W1')->getFont()->setBold(true);
        $sheet->getStyle('A1:W1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:W')->getAlignment()->setVertical('center');
        $sheet->getStyle('A:W')->getAlignment()->setWrapText(true);
    }
}
