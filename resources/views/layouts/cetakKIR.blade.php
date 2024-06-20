<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">

        /* @page {
            size: 595.28pt 841.89pt; /* Ukuran kertas A4 dalam points */
            /* margin: 0; Atur margin sesuai kebutuhan Anda */
        /* } */ */
        body {
            margin: 0;
            /* padding: 20pt; Sesuaikan padding atau margin sesuai kebutuhan */
            font-family: Arial, sans-serif;
        }
        /* .content {
            width: 100%;
            height: 100%;
        } */

        .container {
            /* width: 80%; */
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
            <div class="title">
                <h3 style="text-align:center">@yield('title')</br>
                @yield('desc')</h3>
            </div>
                    
            <div id="description" class="col-md-6" style="line-height: 1.5;" style="margin-top: 10px;">
                <span style="display: inline-block; width: 80px;">Unit Kerja</span>: RSUD Kabupaten Lombok Utara<br />
                <span style="display: inline-block; width: 80px;">Unit</span>: @yield('unit')<br />
                <span style="display: inline-block; width: 80px;">Ruangan</span>: @yield('ruang')<br />
            </div>
            
            @yield('body')

            @php

            $user = \App\Models\Auth\User::whereHas('position', function ($q){
                $q->where('name','Kepala Direktur');
            })->first();

            $logistik = \App\Models\Auth\User::whereHas('position', function ($q){
                $q->where('name','Kepala Seksi Sarana dan Prasarana Logistik');
            })->first();

        @endphp

            <div style="line-height: 1.5; margin-top:10px; text-align:center;">
                <div class="col-md-4" style="float: left; ">
                    <p>Mengetahui , <br/>Direktur RSUD Kabupaten Lombok Utara</p>
                    <p style="margin-top: 75px;">{{ $user->name }} <br /> NIP. {{$user->nip}}</p>
                </div>

                <div class="col-md-4" style="float: right;">
                    <p>Tanjung, {{now()->formatLocalized('%d / %B, %Y')}}<br/> Pengurus Barang
                    <br/></p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">{{ $logistik->name }}<br /> NIP. {{$logistik->nip}}</p>
                </div>
                
            
            </div>

        </div>
    </div>
</body>
</html>