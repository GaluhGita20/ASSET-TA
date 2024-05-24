<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        /* @page {
            size: A4;
            margin: 0;
        } */

        body {
            margin: 0;
            background-color: #ccc;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            padding: 20mm; /* Padding agar konten tidak terlalu dekat dengan tepi kertas */
        }

        .table1 {
            font-family: sans-serif;
            color: #232323;
            border-collapse: collapse;
        }
        
        .table1, th, td {
            border: 1px solid #999;
            padding: 8px 8px;
        }
        

        .container{width:210mm; margin:0 auto; background-color: #fff; height:297mm; padding:20mm; }
    </style>
</head>
<body>
    <div class="container">

        <h4>LAMPIRAN</h4>
        <H4 style="text-align: center;">DAFTAR KEBUTUHAN ALAT DI RUANG {{$record->struct->name}}</br> TAHUN {{$record->procurement_year}}</H4>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Nama Aset</th>
                <th>Spesifikasi</th>
                <th>Standar Kebutuhan</th>
                <th>Jumlah Tersedia</th>
                <th>Jumlah Pengajuan</th>
                <th>Jumlah Disetujui</th>
                <th>Harga Perkiraan / Unit</th>
                <th>Harga Total</th>
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
                    <td>{{$item->requirement_standard}}</td>
                    <td>{{$item->existing_amount}}</td>
                    <td>{{$item->qty_req}}</td>
                    <td>{{$item->qty_agree}}</td>
                    <td>{{(number_format($item->HPS_unit_cost, 0, ',', ','))}}</td>
                    <td>{{(number_format($item->HPS_total_cost, 0, ',', ','))}}</td>
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
                <td colspan="2">Jumlah</td>
                <td>{{$req_standar}}</td>
                <td>{{$exist}}</td>
                <td>{{$req}}</td>
                <td>{{$agree}}</td>
                <td>{{(number_format($unit_cost, 0, ',', ','))}}</td>
                <td>{{(number_format($total_cost, 0, ',', ','))}}</th>
            </tr>
            
        </table>
    </div>
</body>
</html>