<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">

        body {
            margin: 0;
            /* padding: 20pt; Sesuaikan padding atau margin sesuai kebutuhan */
            font-family: Arial, sans-serif;
        }
        /* .content {
            width: 100%;
            height: 100%;
        } */

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

        #asal th, #asal td {
            border: 0px solid #ddd;
            padding: 1px;
            /* margin-left:30px; */
            /* padding: 8px; */
        }

        #petugas th, #petugas td {
            border: 0px solid #ddd;
            /* padding: 0px; */
            /* margin-left:30px; */
            /* padding: 8px; */
        }

        th, td {
            padding: 8px 8px;
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
                    
            {{-- <div id="description" class="col-md-6" style="line-height: 1.5;" style="margin-top: 10px;">
                <span style="display: inline-block; width: 80px;">Provinsi</span>: Nusa Tenggara Barat<br />
                <span style="display: inline-block; width: 80px;">Kabupaten</span>: Lombok Utara<br />
                <span style="display: inline-block; width: 80px;">Daerah</span>: Tanjung<br />
                <span style="display: inline-block; width: 80px;">Unit Kerja</span>: RSUD Kabupaten Lombok Utara<br />
            </div> --}}
            <div id="asal">
                <table style="margin-left:-3px; border:0px; width:30%;" >
                    <tr>
                        <td style="text-align: left;">Provinsi</td>
                        <td>:</td>
                        <td>Nusa Tenggara Barat</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Kabupaten</td>
                        <td>:</td>
                        <td>Lombok Utara</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Daerah</td>
                        <td>:</td>
                        <td>Tanjung</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Unit Kerja</td>
                        <td>:</td>
                        <td>RSUD Kabupaten Lombok Utara</td>
                    </tr>
                </table>
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

            <div id ="petugas">
                <table style="border:0px; ">
                    <tr>
                        <td style="text-align: center;">Mengetahui , <br/>Direktur RSUD Kabupaten Lombok Utara</td>
                        <td style="text-align: center;">{{now()->formatLocalized('%d / %B, %Y')}}<br/> Pengurus Barang</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;" rowspan="8"><b>({{ $user->name }})</b><br /> NIP. {{$user->nip}}</td>
                        <td style="text-align: center;" rowspan="8"><b>({{ $logistik->name }})</b><br /> NIP. {{$logistik->nip}}</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>