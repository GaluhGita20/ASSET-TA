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
            
            <h3 style="text-align:center">KARTU INVENTARIS BARANG KIB C </br>
            (GEDUNG DAN BANGUNAN)</h3>
                    
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
                                <th rowspan="2">Kondisi Bangunan (B, RB, KB)</th>
                                <th colspan="2">Kontruksi Bangunan</th>
                                <th rowspan="2">Luas Lantai (m2)</th>
                                <th rowspan="2">Letak / Lokasi Alamat</th>
                                <th colspan="2" >Dokumen Gedung</th>
                                <th rowspan="2">Luas (M2)</th>
                                <th rowspan="2">Status Tanah</th>
                                <th rowspan="2">Nomor Kode Tanah</th>
                                <th rowspan="2">Asal Usul</th>
                                <th rowspan="2">Harga (Rupiah)</th>
                                <th rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Register</th>
                                <th>Bertingkat</th>
                                <th>Berbeton</th>
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
                            {{-- <td></td> --}}
                            <td>{{$record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-'}}</td>
                            <td>{{$record->condition ? ($record->condition == 'baik' ? ucfirst($record->condition) : ($record->condition == 'rusak berat' ? ucfirst($record->condition) : ucfirst($record->condition))) : '-'}}</td>
                            <td>{{$record->is_graded_bld ? $record->is_graded_bld : '-'}}</td>
                            <td>{{$record->is_concreate_bld ? $record->is_concreate_bld : '-'}}</td>
                            <td>{{$record->wide_bld ? number_format($record->wide_bld, 0, ',', ',')  : '-'}}</td>
                            <td>{{$record->address ? $record->address : '-'}}</td>
                            <td>{{$record->sertificate_date ? Carbon\Carbon::parse($record->sertificate_date)->format('Y-m-d') : '-'}}</td> 
                            <td>{{$record->no_sertificate ? $record->no_sertificate : '-'}}</td>
                            <td>{{$record->wide ? number_format($record->wide, 0, ',', ','): '-'}}</td>
                            <td>{{$record->statusTanah->name ? $record->statusTanah->name : '-'}}</td>
                            <td>{{$record->tanahs->nama_akun ? $record->tanahs->kode_akun.' | '.$record->tanahs->nama_akun : '-'}}</td>
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