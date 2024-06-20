@extends('layouts.cetakAset')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS BARANG KIB E
@endsection

@section('desc')
    (ASET TETAP LAINYA)
@endsection

@section('body')
<div class="row" style="line-height: 1.5; text-align: center;">
    <div style="line-height: 1.5; margin-top:20px;">
        <table class="table1" style="width: 100%;" >
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nama Barang / Jenis Barang</th>
                    <th colspan="2">Nomor</th>
                    <th colspan="2">Buku</th>
                    <th colspan="3">Barang Bercorak Kesenian / Kebudayaan</th>
                    <th colspan="2">Hewan Ternak dan Tumbuhan</th>
                    <th rowspan="2">Jumlah</th>
                    <th rowspan="2">Tahun Cetak /Pembelian</th>
                    <th rowspan="2">Asal Usul</th>
                    <th rowspan="2">Harga (Rupiah)</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th>Kode Barang</th>
                    <th>Register</th>
                    <th>Judul</th>
                    <th>Spesifikasi</th>
                    <th>Asal Daerah</th>
                    <th>Pencipta</th>
                    <th>Bahan</th>
                    <th>Jenis</th>
                    <th>Ukuran</th>
                </tr>
            </thead>
            @php 
                $i = 0;
            @endphp
            @foreach ($records as $record)
            @php 
                $i ++;
            @endphp
            <tr>
                <td style="width: 2px;">{{$i}}</td>
                <td>{{$record->usulans->asetd->name}}</td>
                <td>{{$record->coad ? $record->coad->kode_akun.' | '.$record->coad->nama_akun : '-'}}</td>
                <td>{{$record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-'}}</td>
                <td>{{$record->title ? ucwords($record->title) : '-'}}</td>
                <td>{{$record->spesifikasi ? ucwords($record->spesifikasi) : '-'}}</td>
                <td></td>
                <td>{{$record->creators ? $record->creators : '-'}}</td>
                <td>{{$record->materials->name ? $record->materials->name : '-'}}</td>
                <td>{{$record->tipe_animal ? $record->tipe_animal : '-'}}</td>
                <td>{{$record->size_animal ? $record->size_animal : '-'}}</td>
                <td>{{1}}</td> 
                <td>{{$record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y')}}</td> 
                <td>{{$record->usulans->danad ? $record->usulans->danad->name : '-'}}</td>  
                <td>{{$record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-'}}</td> 
                <td>{{$record->description ? $record->description : '-'}}</td>
            </tr>
            @endforeach


        </table>
        
    </div>
</div>  

@endsection