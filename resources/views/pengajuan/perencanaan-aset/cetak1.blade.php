<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengajuan Sarana dan Prasarana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            /* background-color: #ccc;  */
        }

        .paper{
            /* width:210mm;  */
            margin:0 auto; 
            /* background-color: #fff; 
            height:297mm; 
            padding:20mm;  */
            
        }

        #kop {
            /* display: flex;
            align-items: center;
            justify-content: space-between; */
            margin-bottom: 0px;
        }

        #logo {
            /* width: 50px; */
            /* justify-content: center; */
            /* margin-top: 20px; */
        }

        #judul {
            width: 100%;
            margin-left: 5px;
            text-align: center;
        }

        #judul h2 {
            margin-bottom: 0px;
        }

        #alamat {
            text-align: center;
        }

        #no-surat {
            text-align: left;
            /* border: 0px solid #ddd; */
            /* padding: 8px; */
            /* margin-bottom: 20px; */
        }

        #pembukaan{
            text-align: right;
            margin-bottom: 20px;
        }

        #isi {
            text-align: justify;
            margin-bottom: 50px;
        }

        #ttd {
            text-align: center;
        }

        #ttd table {
            border-collapse: collapse;
            width: 100%;
            /* display: none; */
        }

        #ttd th, #ttd td {
            border: 0px solid #ddd;
            padding: 8px;
        }

        #no-surat th, #no-surat td {
            border: 0px solid #ddd;
            padding: 1px;
            /* margin-left:30px; */
            /* padding: 8px; */
        }

        #judul th, #judul td {
            border: 0px solid #ddd;
            /* padding: 2px; */
            /* padding: 8px; */
        }

        .border{
            border-bottom: 5px solid #000; 
        }

        @page {
            size: A4;
            margin: 2cm;
        }

        @media print {
            #header, #footer {
                display: none;
            }

            body {
                width: 210mm; /* Lebar kertas A4 dalam mm */
                height: 297mm; /* Tinggi kertas A4 dalam mm */
                margin: 0; /* Menghilangkan margin default */
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="paper">
        {{-- <div id="kop"> --}}
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

        <div id="pembukaan" style="line-height: 1.5;">
            <p class="sub-date">Tanjung, {{ \Carbon\Carbon::parse($record->date)->format('j F Y') }}</p>
            <p style="margin-right: 190px;">Kepada</p>
            <p>Yth  :Direktur UPTD Rumah Sakit Umum Daerah</p>
            <p style="margin-right: 95px; margin-top:-10px;">Kabupaten Lombok Utara</p>
            {{-- <p>Di Tempat</p> --}}
            <br>
        </div>

        <div id="no-surat">
            <table style="margin-left:-3px;">
                <tr>
                    <td style="text-align: left;">Nomor</td>
                    <td>:</td>
                    <td>{{$record->code}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Lampiran</td>
                    <td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Perihal</td>
                    <td>:</td>
                    <td>{{$record->regarding}}</td>
                </tr>
                </tr>
            </table>
        </div>
    
        {{-- <div id="no-surat" style="line-height: 1.5; text-align:justify;">
            <p style="display: flex; flex-direction:row; justify-content:space-between;">Nomor : {{$record->code}}</p>
            <p style="display: flex; flex-direction:row; justify-content:space-between;">Lampiran : - <br />
            <p style="display: flex; flex-direction:row; justify-content:space-between;">Perihal : {{$record->regarding}}
            {{-- <p style="display: inline-block; width: 80px; margin-top:-10px;">Perihal</p>: {{$record->regarding}} --}}
    
        <div id="isi" style="line-height: 1.5; text-align:justify;">
            <p>Dengan hormat,<br>Dalam rangka meningkatkan kualitas mutu pelayanan di ruang {{ $record->struct->name }}, kami mengajukan permintaan kebutuhan guna melengkapi sarana dan prasarana ruang {{ $record->struct->name }}. Adapun daftar kebutuhan aset yang kami ajukan terlampir.
            Demikian surat pengajuan ini kami buat, besar harapan agar dapat ditindak lanjuti dan direalisasikan, atas perhatiannya kami sampaikan terima kasih.</p></p>
        </div>

        @php
            $user_pos = auth()->user()->position->location_id;
            $menyetujui_loc = \App\Models\Master\Org\OrgStruct::where('id',$user_pos)->value('parent_id');
            $menyetujui =  \App\Models\Master\Org\OrgStruct::where('id',$menyetujui_loc)->value('name');
            $loc = \App\Models\Master\Org\Position::where('location_id',$menyetujui_loc)->where('level','kepala')->value('id');
            $user_menyetujui = \App\Models\Auth\User::where('position_id',$loc)->value('name');
        @endphp
    
        <div id="ttd">
            <table>
                <tr>
                    <th style="text-align: center;">Mengetahui</th>
                    <th style="text-align: center;">Yang Mengusulkan</th>
                </tr>
                <tr>
                    <td style="text-align: center;" rowspan="8">{{auth()->user()->position->name}}<br>dr. Ahmad Haerul Umam<br>NIP. 19911125202321001</td>
                    <td style="text-align: center;" rowspan="8">{{$menyetujui}}<br>(Sri Adriani, Amd.Keb)<br>NIP. 19911125202321001</td>
                </tr>
            </table>
        </div>

        <div id="ttd">
            <table>
                <tr>
                    <th style="text-align: center;">Mengetahui</th>
                </tr>
                <tr>
                    <td style="text-align: center;" rowspan="8">{{auth()->user()->position->name}}<br>dr. Ahmad Haerul Umam<br>NIP. 19911125202321001</td>
                </tr>
            </table>
        </div>
    
