@extends('layouts.cetakAset')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS BARANG KIB A
@endsection

@section('desc')
    (TANAH)
@endsection

@section('body')
    <div class="row" style="line-height: 1.5; text-align: center;">
        <div style="line-height: 1.5; margin-top:20px;">
            <table class="table1" style="width: 100%;" >
                <thead>
                    <tr>
                        <th rowspan="3">No</th>
                        <th rowspan="3">Nama Barang / Jenis Barang</th>
                        <th colspan="2" rowspan="2"> Nomor</th>
                        <th rowspan="3">Luas (M2)</th>
                        <th rowspan="3">Tahun Pengadaan</th>
                        <th rowspan="3">Letak / Alamat</th>
                        {{-- <th rowspan="3">Status Tanah</th> --}}
                        <th colspan="3" >Status Tanah</th>
                        <th rowspan="3">Penggunaan</th>
                        <th rowspan="3">Asal Usul</th>
                        <th rowspan="3">Harga (Ribuan Rp)</th>
                        <th rowspan="3">Keterangan</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Hak</th>
                        <th colspan="2">Sertifikat</th>
                    </tr>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Register</th>
                        <th>Tanggal</th>
                        <th>Nomor</th>
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
                    <td>{{$record->wide ? number_format($record->wide, 0, ',', ','): '-'}}</td>
                    {{-- <td>{{$record->book_date ? $record->book_date : '-'}}</td> --}}
                    <td>{{$record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y')}}</td> 
                    <td>{{$record->address ? ucwords($record->address) : '-'}}</td>
                    <td>{{$record->hakTanah->name ? $record->hakTanah->name : '-'}}</td>
                    <td>{{$record->sertificate_date ? Carbon\Carbon::parse($record->sertificate_date)->format('Y-m-d') : '-'}}</td>
                    <td>{{$record->no_sertificate ? $record->no_sertificate : '-'}}</td>
                    <td>{{$record->land_use ? ucwords($record->land_use) : '-'}}</td>
                    <td>{{$record->usulans->danad ? $record->usulans->danad->name : '-'}}</td>  
                    <td>{{$record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-'}}</td> 
                    <td>{{$record->description ? $record->description : '-'}}</td>
                </tr>
                @endforeach
            </table>
            
        </div>
    </div> 
@endsection