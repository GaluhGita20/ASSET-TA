<?php

namespace App\Exports\Setting;

use App\Models\Inventaris\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KibEExport implements FromCollection, WithStyles
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
            'G' => 'Kondisi',
            'H' => 'Judul',
            'I' => 'Pencipta',
            'J' => 'Jenis',
            'K' => 'Ukuran (M)',
            'L' => 'Bahan',
            'M' => 'Tahun Perolehan',
            'N' => 'Sumber Perolehan',
            'O' => 'Asal Usul',
            'P' => 'Harga Perolehan (Rupiah)',
            'Q' => 'Masa Manfaat (Tahun)',
            'R' => 'Nilai Residu',
            'S' => 'Akumulasi Penyusutan (Rupiah)',
            'T' => 'Nilai Buku (Rupiah)',
            'U' => 'Unit',
            'V' => 'Lokasi',
            'W' => 'Keterangan',
        ];
    }

    public function collection()
    {
        $data = [];
        $data[] = array_values($this->thead());
        // $records = Aset::grid()->where('type','KIB B')->filters()->get();
        $records = Aset::where('type','KIB E')->grid()->filters()->get();
        foreach ($records as $i => $record) {
            $data[] = [
                ($i + 1),
                $record->usulans ? $record->usulans->asetd->name : '-',
                $record->coad ? $record->coad->kode_akun.'|'.$record->coad->nama_akun : '-',
                $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-',
                $record->book_date ? $record->book_date : '-',
                $record->status ? ($record->status == 'actives' ? ucfirst('active') : ($record->status == 'notactive' ? ucfirst($record->status) : ($record->status == 'in repair' ? ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))):'-',
                $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                $record->title ? ucwords($record->title) : '-',
                $record->creators ? $record->creators : '-',
                $record->tipe_animal ? $record->tipe_animal : '-',
                $record->size_animal ? $record->size_animal : '-',
                $record->materials->name ? $record->materials->name : '-',
                $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y'),
                $record->usulans->danad ? $record->usulans->danad->name : '-',    
                $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ? ucfirst($record->usulans->trans->source_acq) : ucfirst($record->usulans->trans->source_acq),
                $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ','),          
                $record->useful ? $record->useful : '-',
                $record->residual_value ? number_format($record->residual_value, 0, ',', ',')  : '-',
                $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                $record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-', 
                !empty($record->usulans->perencanaan->struct) ? $record->usulans->perencanaan->struct->name : ($record->location_hibah_aset ? $record->deps->name : '-'),
                $record->locations ? $record->locations->name : $record->non_room_location,
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
