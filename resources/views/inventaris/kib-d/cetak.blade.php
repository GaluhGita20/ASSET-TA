@extends('layouts.cetakAset')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS BARANG KIB D
@endsection

@section('desc')
    (JALAN BANGUNAN IRIGASI)
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
                        <th rowspan="2">Kontruksi</th>
                        <th rowspan="2">Panjang (Km)</th>
                        <th rowspan="2">Lebar (M)</th>
                        <th rowspan="2">Luas (M2)</th>
                        <th rowspan="2">Letak / Lokasi Alamat</th>
                        <th colspan="2" >Dokumen</th>
                        <th rowspan="2">Status Tanah</th>
                        <th rowspan="2">Nomor Kode Tanah</th>
                        <th rowspan="2">Asal Usul</th>
                        <th rowspan="2">Harga (Rupiah)</th>
                        <th rowspan="2">Kondisi (B, RB, KB)</th>
                        <th rowspan="2">Keterangan</th>
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
                    <td>{{$record->usulans? $record->usulans->trans->vendors->name : '-'}}</td>
                    <td>{{$record->long_JJR ? $record->long_JJR : '-'}}</td>
                    <td>{{$record->wide ? number_format($record->wide, 0, ',', ','): '-'}}</td>
                    <td>{{$record->width_JJR ? $record->width_JJR : '-'}}</td>
                    <td>{{$record->address ? $record->address : '-'}}</td>
                    <td>{{$record->sertificate_date ? Carbon\Carbon::parse($record->sertificate_date)->format('Y-m-d') : '-'}}</td> 
                    <td>{{$record->no_sertificate ? $record->no_sertificate : '-'}}</td>
                    <td>{{$record->statusTanah->name ? $record->statusTanah->name : '-'}}</td>
                    <td>{{$record->tanahs->nama_akun ? $record->tanahs->kode_akun.'/'.$record->tanahs->nama_akun : '-'}}</td>
                    <td>{{$record->usulans->danad ? $record->usulans->danad->name : '-'}}</td>  
                    <td>{{$record->book_value ? number_format($record->book_value, 0, ',', ',')  : '-'}}</td> 
                    <td>{{$record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-'}}</td>
                    <td>{{$record->description ? $record->description : '-'}}</td>
                </tr>
                @endforeach


            </table>
            
        </div>
    </div> 
@endsection