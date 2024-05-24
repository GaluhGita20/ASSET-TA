<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        @page {
            size: A4;
            margin: 0;
        }

        /* body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            padding: 20mm; /* Padding agar konten tidak terlalu dekat dengan tepi kertas */
        /* }

        .page-break {
            page-break-before: always;
        } */

        body{font-family: Arial, Helvetica, sans-serif; font-size:12px;  background-color: #ccc}
        .rangkasurat{width:210mm; margin:0 auto; background-color: #fff; height:297mm; padding:20mm; }
        table{border-bottom: 5px solid #000; padding: 2px}
        .tengah{text-align: center; line-height: 5px;}
        #tls{text-align: right; padding-right: 240px;}
        .alamat-tujuan{margin-left:50%;}

    </style>
</head>
<body>
    <div class="page1">
        <div class="rangkasurat">
            <table width="100%">
                <tr>
                    <td><img src="{{'/'.(config('base.logo.auth'))}}" width="140px"></td>
                    <td class="tengah">
                        <h3>PEMERINTAH KABUPATEN LOMBOK UTARA</h3>
                        <h2>UPTD BLUD RUMAH SAKIT UMUM DAERAH</h2>
                        <h2>KABUPATEN LOMBOK UTARA</h2>
                        <h5 style="margin-top: -4px; margin-bottom: -4px; font-weight:100">Jln Raya Tiok Tata Tunaq Tanjung Kode Pos : 83352</h5>
                        <h5 style="top:-5px">Telp. (0370) 6123019 (fax. 90370) 6123019 e_mail : rsud_klu@yahoo.com</h5>
                    </td>
                    <td><img src="{{'/'.(config('base.logo.auth'))}}" width="140px"></td>
                </tr>
            </table>
    
            <div id="tgl-srt" class="col-md-6">
                <p id="tls">Tanjung, {{ \Carbon\Carbon::parse($record->date)->format('j F Y') }}</p>
                
                <p class="alamat-tujuan" style="line-height: 1.5; margin-top:30px;">Kepada <br /></br></br>
                Yth. :Direktur UPTD Rumah Sakit Umum Daerah Kabupaten Lombok Utara</p>
                
            </div>
            <div id="lampiran" class="col-md-6" style="line-height: 1.5;">
                <span style="display: inline-block; width: 80px;">Nomor</span>: {{$record->code}}<br />
                <span style="display: inline-block; width: 80px;">Lampiran</span>: - <br />
                <span style="display: inline-block; width: 80px;">Perihal</span>: {{$record->regarding}}
            </div>
            <div id="text-body" style="margin-top: 90px; line-height: 1.5; text-align:justify;" >
                <p>Dengan hormat,<br>Dalam rangka meningkatkan kualitas mutu pelayanan di ruang {{ $record->struct->name }}, kami mengajukan permintaan kebutuhan guna melengkapi sarana dan prasarana ruang {{ $record->struct->name }}. Adapun daftar kebutuhan aset yang kami ajukan terlampir.
                Demikian surat pengajuan ini kami buat, besar harapan agar dapat ditindak lanjuti dan direalisasikan, atas perhatiannya kami sampaikan terima kasih.</p></p>
                {{-- Lorem, ipsum dolor sit amet consectetur adipisicing elit. Repudiandae ut harum ipsam modi quia tempore eum asperiores hic. Molestiae neque totam excepturi molestias ex fuga provident tenetur placeat? Hic, nesciunt. --}}
            </div>
    
            <div class="row" style="line-height: 1.5; text-align: center;">
                <div class="col-md-4" style="margin-top: 100px; float: right; padding-right: 100px;">
                    <p>Mengetahui <br/> Koordinator Dokter IGD</p>
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
                
                <div class="col-md-4" style="margin-top: 100px; float: left; padding-left: 100px;">
                    <p>Yang Mengusulkan <br/> {{auth()->user()->position->name}}</p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
            </div>
            <div class="row" style="line-height: 1.5; text-align: center; margin-top:350px;">
                <div class="col-md-12">
                    <p>Menyetujui <br/> {{auth()->user()->position->name}}</p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
            </div>
            
            
    
        </div>
    </div>



</body>
</html>