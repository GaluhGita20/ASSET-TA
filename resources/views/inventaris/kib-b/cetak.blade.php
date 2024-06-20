@extends('layouts.cetakAset')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS BARANG KIB B
@endsection

@section('desc')
    (PERALATAN DAN MESIN)
@endsection

@section('body')
    {{-- <div class="row" style="line-height: 1.5; text-align: center;"> --}}
        <div style="line-height: 1.5; margin-top:20px;">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Kode Barang</th>
                        <th rowspan="2">Nama Barang / Jenis Barang</th>
                        <th rowspan="2">Nomor Register</th>
                        <th rowspan="2">Merek Type</th>
                        <th rowspan="2">Ukuran / CC</th>
                        <th rowspan="2">Bahan</th>
                        <th rowspan="2">Tahun Pembelian</th>
                        <th colspan="5">Nomor</th>
                        <th rowspan="2">Asal Usul Cara Perolehan</th>
                        <th rowspan="2">Harga (Ribuan Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Pabrik</th>
                        <th>Rangka</th>
                        <th>Mesin</th>
                        <th>Polisi</th>
                        <th style="border: 1px solid black;">BPKB</th>
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
                    {{-- <td></td> --}}
                    <td>{{$record->coad ? $record->coad->kode_akun.' | '.$record->coad->nama_akun : '-'}}</td>
                    <td>{{$record->usulans->asetd->name}}</td>
                    <td>{{$record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-'}}</td>
                    <td>{{$record->merek_type_item ? ucwords($record->merek_type_item) : '-'}}</td>
                    <td>{{$record->cc_size_item ? $record->cc_size_item : '-'}}</td>
                    <td>{{$record->materials->name ? $record->materials->name : '-'}}</td>
                    <td>{{$record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y')}}</td>
                    <td>{{$record->no_factory_item ? $record->no_factory_item : '-'}}</td>
                    <td>{{$record->no_frame ? $record->no_frame : '-'}}</td>
                    <td>{{$record->no_machine_item ? $record->no_machine_item : '-'}}</td>
                    <td>{{$record->no_police_item ? $record->no_police_item : '-'}}</td>
                    <td>{{$record->no_BPKB_item ? $record->no_BPKB_item : '-'}}</td> 
                    <td>{{$record->usulans->danad ? $record->usulans->danad->name : '-'}}</td>  
                    <td>{{$record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-'}}</td> 
                    <td>{{$record->description ? $record->description : '-'}}</td>
                </tr>
                @endforeach
            </table>

        </div>
    {{-- </div>  --}}
@endsection
