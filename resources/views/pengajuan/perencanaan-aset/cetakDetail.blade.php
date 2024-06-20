<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            /* background-color: #ccc;  */
        }

        .paper{
            /* width:210mm;  */
            margin:0 auto; 
            background-color: #fff; 
            /* height:297mm;  */
            /* padding:20mm;  */
            
        }

        .border{
            border-bottom: 5px solid #000; 
        }

        #kop {
            margin-bottom: 0px;
        }

        #logo {
            width: 100px;
        }

        #judul {
            text-align: center;
        }

        #judul h2 {
            margin-bottom: 0px;
        }

        #alamat {
            text-align: center;
        }

        .table1 {
            font-family: sans-serif;
            color: #232323;
            border-collapse: collapse;
        }
        
        .table1, th, td {
            border: 1px solid #999;
            padding: 8px 8px;
            text-align: justify;
        }

        #judul th, #judul td {
            border: 0px solid #ddd;
            /* padding: 2px; */
            /* padding: 8px; */
        }
        
    </style>
</head>
<body>
    <div class="paper">
        <div id="judul">
            <table>
                <td style="align-items:flex-end;">
                    <img id="logo" src="{{$gambar_logo_1}}" alt="Logo Pemda Lombok Utara" style="width:90px; margin-top:20px;" >
                </td>
                <td>
                    <div class="text" style="text-align: center;">
                        <h2 style="margin: 0;">PEMERINTAH KABUPATEN LOMBOK UTARA<br>
                            UPTD BLUD RUMAH SAKIT UMUM DAERAH <br>
                            KABUPATEN LOMBOK UTARA</h2>
                        <p style="line-height: 1.5; margin: 0;">Jln Raya Tiok Tata Tunaq, Tanjung Lombok Utara Kode Pos: 83352</p>
                        <h4 style="margin: 5px 0;">Telp. (0370) 6123019, fax (0370) 6123010, Email: rsudklu@gmail.com</h4>
                    </div>
                </td>
                <td style="align-items:flex-end;">
                    <img id="logo" src="{{$gambar_logo_2}}" alt="Logo Pemda Lombok Utara" style="width:100px; margin-top:10px;">
                </td>
            </table>
        </div>
    <div class="border"></div>
        {{-- <div id="kop">
            <div id="judul" style="display:flex; flex-direction:row; align-items:center; justify-content:space-around; width:100%; ">
                <img id="logo" src={{ public_path('images/logo.png') }}  style="width: 100px;">
                <div class="text">
                    <h2>PEMERINTAH KABUPATEN LOMBOK UTARA</br>
                        UPTD BLUD RUMAH SAKIT UMUM DAERAH </br>
                        KABUPATEN LOMBOK UTARA</h2>
                        <p style="line-height: 1.5; margin-top:-2px;">Jln Raya Tiok Tata Tunaq, Tanjung Lombok Utara Kode Pos: 83352</p>
                        <h4 style="margin-top: -10px;">Telp. (0370) 6123019, fax (0370) 6123010, Email: rsudklu@gmail.com</h4>
                </div>
                <img id="logo" src="{{'/'.(config('base.logo.auth'))}}" alt="Logo Pemda Lombok Utara">
            </div>
        </div>
        <div class="border"></div> --}}
        <H4 style="text-align: center;">DAFTAR KEBUTUHAN ALAT DI RUANG {{strtoupper($record->struct->name)}}</br> TAHUN {{$record->procurement_year}}</H4>
        <table class="table1">
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Nama Aset</th>
                <th style="text-align: center;">Spesifikasi</th>
                <th style="text-align: center;">Standar Kebutuhan</th>
                <th style="text-align: center;">Jumlah Tersedia</th>
                <th style="text-align: center;">Jumlah Pengajuan</th>
                <th style="text-align: center;">Jumlah Disetujui</th>
                <th style="text-align: center;">Harga Perkiraan / Unit</th>
                <th style="text-align: center;">Harga Total</th>
            </tr>
            @php 
                $i = 1;
            @endphp
            @foreach ($detail as $item)    
                <tr>
                    <td style="width: 2px;">{{$i++}}</td>
                    <td>{{$item->asetd->name}}</td>
                    {{-- <td style="width: 80px;"><p style="text-align: justify;">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Assumenda corporis tenetur nobis, repellendus earum, --}}
                    {{-- sunt hic, culpa iusto reiciendis nihil natus. Quis consequuntur numquam doloremque nisi consectetur dolore, eveniet officia?</p></td> --}}
                    <td>{{$item->desc_spesification}}</td>
                    <td style="text-align: center">{{$item->requirement_standard}}</td>
                    <td style="text-align: center">{{$item->existing_amount}}</td>
                    <td style="text-align: center">{{$item->qty_req}}</td>
                    <td style="text-align: center">{{$item->qty_agree}}</td>
                    <td style="text-align: center">{{(number_format($item->HPS_unit_cost, 0, ',', ','))}}</td>
                    <td style="text-align: center">{{(number_format($item->HPS_total_cost, 0, ',', ','))}}</td>
                </tr>
            @endforeach
            @php
                $req_standar = 0;
                $exist = 0;
                $req = 0;
                $agree = 0;
                $unit_cost = 0;
                $total_cost = 0;

                foreach ($detail as $key => $value) {
                    $req_standar += $value->requirement_standard;
                    $exist += $value->existing_amount;
                    $req += $value->qty_req;
                    $agree += $value->qty_agree;
                    $unit_cost += $value->HPS_unit_cost;
                    $total_cost += $value->HPS_total_cost;
                }

            @endphp
            <tr>
                <td></th>
                <td colspan="2" style="text-align: center">Jumlah</td>
                <td style="text-align: center">{{$req_standar}}</td>
                <td style="text-align: center">{{$exist}}</td>
                <td style="text-align: center">{{$req}}</td>
                <td style="text-align: center">{{$agree}}</td>
                <td style="text-align: center">{{(number_format($unit_cost, 0, ',', ','))}}</td>
                <td style="text-align: center">{{(number_format($total_cost, 0, ',', ','))}}</th>
            </tr>
            
        </table>
    </div>
</body>
</html>