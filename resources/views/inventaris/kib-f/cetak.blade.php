@extends('layouts.cetakAset')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS BARANG KIB F
@endsection

@section('desc')
    (KONTRUKSI DALAM PENGERJAAN)
@endsection

@section('body')
    <div class="row" style="line-height: 1.5; text-align: center;">
        <div style="line-height: 1.5; margin-top:20px;">
            <table class="table1" style="width: 100%;" >
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Barang / Jenis Barang</th>
                        <th rowspan="2">Bangunan (P, SP. D)</th>
                        <th colspan="2">Kontruksi Bangunan</th>
                        <th rowspan="2">Luas (M2)</th>
                        <th rowspan="2">Letak / Lokasi Alamat</th>
                        <th colspan="2" >Dokumen</th>
                        <th rowspan="2">Tgl/Bln/Thn Mulai</th>
                        <th rowspan="2">Status Tanah</th>
                        <th rowspan="2">Nomor Kode Tanah</th>
                        <th rowspan="3">Nilai Kontrak (ribuan Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Bertingkat / Tidak</th>
                        <th>Beton / Tidak</th>
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
                    <td>{{$record->character_bld ? $record->character_bld : '-'}}</td>
                    <td>{{$record->is_graded_bld ? $record->is_graded_bld : '-'}}</td>
                    <td>{{$record->is_concreate_bld ? $record->is_concreate_bld : '-'}}</td>
                    <td>{{$record->wide ? number_format($record->wide, 0, ',', ','): '-'}}</td>
                    <td>{{$record->address ? $record->address : '-'}}</td>
                    <td>{{$record->sertificate_date ? Carbon\Carbon::parse($record->sertificate_date)->format('Y-m-d') : '-'}}</td>
                    <td>{{$record->no_sertificate ? $record->no_sertificate : '-'}}</td>
                    {{-- <td>{{$record->coad ? $record->coad->kode_akun.' | '.$record->coad->nama_akun : '-'}}</td> --}}
                    {{-- <td>{{$record->sertificate_date ? Carbon\Carbon::parse($record->sertificate_date)->format('Y-m-d') : '-'}}</td>  --}}
                    {{-- <td>{{$record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-'}}</td> --}}
                    <td>{{$record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y-m-d') : $record->usulans->trans->receipt_date->format('Y-m-d')}}</td>  
                    <td>{{$record->statusTanah->name ? $record->statusTanah->name : '-'}}</td>
                    {{-- <td>{{$record->statusTanah->name ? $record->statusTanah->name : '-'}}</td> --}}
                    <td>{{$record->tanahs->nama_akun ? $record->tanahs->kode_akun.'/'.$record->tanahs->nama_akun : '-'}}</td>
                    {{-- <td>{{$record->usulans->danad ? $record->usulans->danad->name : '-'}}</td>   --}}
                    <td>{{$record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-'}}</td> 
                    <td>{{$record->description ? $record->description : '-'}}</td>
                </tr>
                @endforeach


            </table>
            
        </div>
    </div>    
    @php

        $user = \App\Models\Auth\User::whereHas('position', function ($q){
            $q->where('name','Kepala Direktur');
        })->first();

        $logistik = \App\Models\Auth\User::whereHas('position', function ($q){
            $q->where('name','Kepala Seksi Sarana dan Prasarana Logistik');
        })->first();

    @endphp
@endsection
