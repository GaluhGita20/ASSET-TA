@extends('layouts.cetakKIR')

{{-- @section('action', route($routes . '.storeDetailKibE')) --}}

@section('title')
    KARTU INVENTARIS RUANG
@endsection

@section('desc')
    (KIB E)
@endsection

@section('ruang')
    {{$ruang}}
@endsection

@section('unit')
    {{$unit->orgLocation->name}}
@endsection

@section('body')
    @php
    $inventaris = $records;
    $groupedInventaris = [];
    $i =0;

    // Menggabungkan jumlah barang yang sama
    foreach ($records as $item) {
        $namaBarang = $item->usulans->asetd->name;
        $tahunBeliAwal = $item->usulans->trans->spk_start_date ? $item->usulans->trans->spk_start_date->format('Y') : $item->usulans->trans->receipt_date->format('Y');
        $kode =  $item->coad ? $item->coad->kode_akun.' | '.$item->coad->nama_akun : '-';
        $dana =  $item->usulans->danad ? $item->usulans->danad->name : '-';


        if (isset($groupedInventaris[$namaBarang])) {
            $groupedInventaris[$namaBarang]['jumlah'] += 1;
            if($groupedInventaris[$namaBarang]['tahun_awal'] <  $tahunBeliAwal ){
                $groupedInventaris[$namaBarang] = [
                    'tahun_awal' => $tahunBeliAwal
                ];
            }elseif($groupedInventaris[$namaBarang]['tahun_awal'] >  $tahunBeliAwal )
            {
                $groupedInventaris[$namaBarang] = [
                    'tahun_akhir' => $tahunBeliAwal
                ];
            }

            if($item->condition == 'baik'){
                $groupedInventaris[$namaBarang]['jumlah_baik'] +=1;
            }elseif($item->condition == 'rusak ringan'){
                $groupedInventaris[$namaBarang]['jumlah_rr'] +=1;
            }else{
                $groupedInventaris[$namaBarang]['jumlah_rb'] +=1;
            }

            if($groupedInventaris[$namaBarang]['dana'] != $dana){
                $groupedInventaris[$namaBarang][] = $dana;
            }
        } else {
            if ($item->condition == 'baik') {
                $jumlahBaik = 1;
                $jumlahRR = 0;
                $jumlahRB = 0;
            } elseif ($item->condition == 'rusak ringan') {
                $jumlahBaik = 0;
                $jumlahRR = 1;
                $jumlahRB = 0;
            } else {
                $jumlahBaik = 0;
                $jumlahRR = 0;
                $jumlahRB = 1;
            }

            $groupedInventaris[$namaBarang] = [
                'nama' => $namaBarang,
                'jumlah' => 1,
                'kode' => $kode,
                'tahun_awal' => $tahunBeliAwal,
                'tahun_akhir' => $tahunBeliAwal,
                'dana' => [$dana],
                'jumlah_baik' => $jumlahBaik,
                'jumlah_rr' => $jumlahRR,
                'jumlah_rb' => $jumlahRB,
            ];
        }
    }

    // Mengubah array asosiatif menjadi array numerik untuk di-loop di view
    $groupedInventaris = array_values($groupedInventaris);
    @endphp

    <div class="row" style="line-height: 1.5; text-align: center;">
        <div style="line-height: 1.5; margin-top:20px;">
            <table class="table1" style="width: 100%;" >
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Barang / Jenis Barang</th>
                        <th rowspan="2">Kode Barang</th>
                        <th rowspan="2">Tahun Pembuatan / Pembelian</th>
                        <th rowspan="2">Jumlah Barang</th>
                        <th rowspan="2">Harga Beli</th>
                        <th colspan="3">Keadaan Barang</th>
                        <th rowspan="2">Sumber Dana</th>
                        {{-- <th rowspan="2">Keterangan</th> --}}
                    </tr>
                    <tr>
                        <th>Baik</th>
                        <th>Kurang Baik</th>
                        <th>Rusak Berat</th>
                    </tr>
                </thead>
                
                
                @php 
                    $i = 0;
                @endphp
                @foreach ($groupedInventaris as $record)
                @php 
                    $i ++;
                @endphp 
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$record['nama']}}</td>
                    <td>{{$record['kode']}}</td>
                    @if($record['tahun_awal'] == $record['tahun_akhir'])
                        <td>{{$record['tahun_awal']}}</td>
                    @else
                        <td>{{$record['tahun_awal']}} - {{$record['tahun_akhir']}}</td>
                    @endif
                    <td>{{$record['jumlah']}}</td>
                    <td></td>
                    <td>{{$record['jumlah_baik']}}</td>
                    <td>{{$record['jumlah_rr']}}</td>
                    <td>{{$record['jumlah_rb']}}</td>
                    <td>{{implode(', ', $record['dana'])}}</td>
                </tr>

                @endforeach  
            </table>
        </div>
    </div>
@endsection
