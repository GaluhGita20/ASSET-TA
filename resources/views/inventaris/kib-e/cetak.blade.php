<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
         @page {
            size: 595.28pt 841.89pt; /* Ukuran kertas A4 dalam points */
            margin: 0; /* Atur margin sesuai kebutuhan Anda */
        }
        body {
            margin: 0;
            padding: 20pt; /* Sesuaikan padding atau margin sesuai kebutuhan */
            font-family: Arial, sans-serif;
        }
        .content {
            width: 100%;
            height: 100%;
        }

            .container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>

    </style>
</head>
<body>
    <div class="page1">
        <div class="rangkasurat">
            
            <h3 style="text-align:center">KARTU INVENTARIS BARANG KIB E </br>
            (ASET TETAP LAINYA)</h3>
                    
            <div id="lampiran" class="col-md-6" style="line-height: 1.5;" style="margin-top: 150px;">
                <span style="display: inline-block; width: 80px;">Provinsi</span>: Nusa Tenggara Barat<br />
                <span style="display: inline-block; width: 80px;">Kabupaten</span>: Lombok Utara<br />
                <span style="display: inline-block; width: 80px;">Daerah</span>: Tanjung<br />
                <span style="display: inline-block; width: 80px;">Unit Kerja</span>: RSUD Kabupaten Lombok Utara<br />
            </div>
    
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
                        @if($record->usulans->asetd->name == $record->usulans->asetd->name + 1 )


                        @else
                            
                        @endif

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
            @php

                $user = \App\Models\Auth\User::whereHas('position', function ($q){
                    $q->where('name','Kepala Direktur');
                })->first();

                $logistik = \App\Models\Auth\User::whereHas('position', function ($q){
                    $q->where('name','Kepala Seksi Sarana dan Prasarana Logistik');
                })->first();

            @endphp
            <div style="line-height: 1.5; margin-top:20px; text-align:center;">
                <div class="col-md-4" style="float: left; ">
                    <p>Mengetahui , <br/>Direktur RSUD Kabupaten Lombok Utara</p>
                    <p style="margin-top: 75px;">{{ $user->name }} <br /> NIP. {{$user->nip}}</p>
                </div>

                <div class="col-md-4" style="float: right;">
                    <p>{{now()->formatLocalized('%d / %B, %Y')}}<br/> Pengurus Barang
                    <br/></p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">{{ $logistik->name }}<br /> NIP. {{$logistik->nip}}</p>
                </div>
                
            
            </div>  
        </div>
    </div>



</body>
</html>