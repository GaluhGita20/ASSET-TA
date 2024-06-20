<?php

namespace App\Exports\Setting;

use App\Models\Inventaris\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KibCExport implements FromCollection, WithStyles
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
            'H'=>'Bertingkat',
            'I'=>'Berbeton',
            'J'=>'Luas Lantai (m2)',
            'K'=>'Luas Bangunan (m2)',
            'L'=>'Alamat',
            'M'=>'Tahun Perolehan',
            'N'=>'Status Tanah',
            'O'=>'Nomor Sertifikat',
            'P'=>'Tanggal Sertifikat',
            'Q' => 'Sumber Perolehan',
            'R' => 'Tahun Perolehan',
            'S' => 'Asal Usul',
            'T' => 'Kode Tanah',
            'U' => 'Harga Perolehan (Rupiah)',
            'V' => 'Masa Manfaat (Tahun)',
            'W' => 'Nilai Residu',
            'X' => 'Akumulasi Penyusutan (Rupiah)',
            'Y' => 'Nilai Buku (Rupiah)',
            'Z' => 'Unit Pengusul',
            'AA' => 'Keterangan',
        ];
    }

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {

        $query = Aset::with('coad')->grid()
        ->where('type', 'KIB C')
        ->whereIn('status', ['actives', 'in repair', 'in deletion', 'maintenance']);
    
    if ($this->filters['jenis_aset'] !== null) {
        $query->where('jenis_aset', $this->filters['jenis_aset']);
    }
    
    if ($this->filters['room_location'] !== null) {
        $query->where('room_location', $this->filters['room_location']);
    }
    
    if ($this->filters['location_id'] !== null) {
        $req = $this->filters['location_id'];
        $query->where(function ($query) use ($req) {
            $query->whereHas('usulans', function ($q) use ($req) {
                $q->whereHas('perencanaan', function ($qq) use ($req) {
                    $qq->where('struct_id', $req); // Menggunakan $req untuk menghindari masalah referensi
                });
            })->orWhere('location_hibah_aset', $req); // Menggunakan $req untuk konsistensi
        });
    }
    
    if ($this->filters['condition'] !== null) {
        $query->where('condition', $this->filters['condition']);
    }
    
    // Mendapatkan hasil query
    $results = $query->get();

    
        $data = [];
        $data[] = array_values($this->thead());
        // $records = Aset::grid()->where('type','KIB B')->filters()->get();
        //$records = Aset::where('type','KIB C')->grid()->filters()->get();
        foreach ($results as $i => $record) {
            $data[] = [
                ($i + 1),
                $record->usulans ? $record->usulans->asetd->name : '-',
                $record->coad ? $record->coad->kode_akun.'|'.$record->coad->nama_akun : '-',
                $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-',
                $record->book_date ? $record->book_date : '-',
                $record->status ? ($record->status == 'actives' ? ucfirst('active') : ($record->status == 'notactive' ? ucfirst($record->status) : ($record->status == 'in repair' ? ucfirst($record->status) : ($record->status == 'in deletion' ? ucfirst($record->status) : ucfirst($record->status))))):'-',
                $record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-',
                $record->is_graded_bld ? $record->is_graded_bld : '-',
                $record->is_concreate_bld ? $record->is_concreate_bld : '-',
                $record->wide ? number_format($record->wide, 0, ',', ','): '-',
                $record->wide_bld ? number_format($record->wide_bld, 0, ',', ',')  : '-',
                $record->address ? $record->address : '-',
                $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y'),
                $record->statusTanah->name ? $record->statusTanah->name : '-',
                $record->no_sertificate ? $record->no_sertificate : '-',
                $record->sertificate_date ? Carbon::parse($record->sertificate_date)->formatLocalized('%d/%B/%Y') : '-', 
                $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y'),  
                $record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ? ucfirst($record->usulans->trans->source_acq) : ucfirst($record->usulans->trans->source_acq),
                $record->usulans->danad ? $record->usulans->danad->name : '-',  
                $record->tanahs->nama_akun ? $record->tanahs->kode_akun.'/'.$record->tanahs->nama_akun : '-',
                $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ','),          
                $record->useful ? $record->useful : '-',
                $record->residual_value ? number_format($record->residual_value, 0, ',', ',')  : '-',
                $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0',
                $record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-', 
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
        $sheet->getColumnDimension('X')->setWidth(30);
        $sheet->getColumnDimension('Y')->setWidth(80);
        $sheet->getColumnDimension('Z')->setWidth(30);
        $sheet->getColumnDimension('AA')->setWidth(30);
        $sheet->getStyle('A1:AA1')->getFont()->setBold(true);
        $sheet->getStyle('A1:AA1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:AA')->getAlignment()->setVertical('center');
        $sheet->getStyle('A:AA')->getAlignment()->setWrapText(true);
    }
}
