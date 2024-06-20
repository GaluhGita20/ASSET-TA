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
            background-color: #ccc; 
        }

        .paper{
            width:210mm; 
            margin:0 auto; 
            background-color: #fff; 
            height:297mm; 
            padding:20mm; 
            
        }

        #kop {
            /*display: flex;*/
            /*align-items: center;*/
            /*justify-content: space-between;*/
            margin-bottom: 0px;
        }

        #logo {
            width: 100px;
            /*justify-content: center;*/
             /*margin-top: -40px; */
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

        #no-surat {
            text-align: left;
            margin-bottom: 20px;
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
        }

        #ttd th, #ttd td {
            border: 1px solid #ddd none;
            padding: 8px;
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
        <div id="kop">
            <div id="judul" style="display:flex; flex-direction:row; align-items:center; justify-content:space-around; width:100%; ">
                <img id="logo" src="/assets/images/KLU_logo.png" alt="Logo Pemda Lombok Utara" style="width: 100px;">
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
        <div class="border"></div>

        <p class="sub-date" style="text-align:right;">Tanjung, {{ \Carbon\Carbon::parse($record->date)->format('j F Y') }}</p>
        
        <div id="pembukaan" style="display:flex; flex-direction:row; justify-content:space-between;">
            <div id="no-surat" style="line-height: 1.5; text-align:justify;">
                <p style="display: inline-block; width: 80px;">Nomor</p>: {{$record->code}}<br />
                <p style="display: inline-block; width: 80px; margin-top:-10px;">Lampiran</p>: - <br />
                <p style="display: inline-block; width: 80px; margin-top:-10px;">Perihal</p>: {{$record->regarding}}
            </div>
            <div id="teks" style="line-height: 1.5;">
                <p style="margin-right: 190px;">Kepada</p>
                <p>Yth  :Direktur UPTD Rumah Sakit Umum Daerah</p>
                <p style="margin-right: 95px; margin-top:-10px;">Kabupaten Lombok Utara</p>
            </div>

        </div>
    
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
                    <th>Mengetahui</th>
                    <th>Yang Mengusulkan</th>
                </tr>
                <tr>
                    <td>{{auth()->user()->position->name}}<br>dr. Ahmad Haerul Umam<br>NIP. 19911125202321001</td>
                    <td>{{$menyetujui}}<br>(Sri Adriani, Amd.Keb)<br>NIP. 19911125202321001</td>
                </tr>
            </table>
    </div>