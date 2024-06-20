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

        .letter-spacing {
            letter-spacing: 1.5px;
        }
        .word-spacing {
            word-spacing: 1.5px;
        }
        .line-height {
            line-height: 1.5;
        }

        .paragraph-spacing {
            margin-bottom: 20px;
            padding-top: 10px;
        }

        /* kop surat */
        .header-surat{
            font-family: Arial, Helvetica;
            font-size: 14px;
        }

        .sub-header{
            font-weight: bold;
            font-size: 12px;        
        }
        /* kop surat */


        /* body surat */
        .body-surat{
            font-family: "Times New Roman", Times, serif;
            font-size:12px;  
        }
        /* body surat */


        /* footer surat */
        .footer-surat{
            font-family: "Times New Roman", Times, serif; 
            font-size:12px;  
        }
        /* footer surat */

        body{
            font-family: Arial, Helvetica, 
            sans-serif; font-size:12px;  
            background-color: #ccc; 
        }
        
        .rangka-surat{
            width:210mm; 
            margin:0 auto; 
            background-color: #fff; 
            height:297mm; 
            padding:20mm; 
        }
        table{
            border-bottom: 5px solid #000; 
            padding-top: 0px;
        }
        .main-header{
            text-align: center; 
            line-height: 5px;
        }

        .sub-date{
            text-align: right;
            padding-right: 10px;
        }
        .alamat-tujuan{
            margin-left:50%;
        }

    </style>
</head>
<body>
    <div class="rangka-surat">
        <div class="header-surat">
            <table width="100%">
                <tr>
                    <td><img src="{{'/'.(config('base.logo.auth'))}}" width="140px"></td>
                    <td class="main-header paragraph-spacing ">
                        <h2>PEMERINTAH KABUPATEN LOMBOK UTARA</h2>
                        <h2>UPTD BLUD RUMAH SAKIT UMUM DAERAH</h2>
                        <h2>KABUPATEN LOMBOK UTARA</h2>
                        <div class="sub-header">
                            <p style="margin-top:2px; ">Jln Raya Tiok Tata Tunaq, Tanjung Lombok Utara Kode Pos: 83352</p>
                            <p style="margin-top: -1px;">Telp. (0370) 6123019, fax (0370) 6123010, Email: rsudklu@gmail.com</p>
                        </div>
                    </td>
                    <td><img src="/assets/images/KLU_logo.png" width="140px"></td>
                </tr>
            </table>
        </div>

        <div class="body-surat">
            <div id="tujuan" class="col-md-6 word-spacing  paragraph-spacing">
                <p class="sub-date">Tanjung, {{ \Carbon\Carbon::parse($record->date)->format('j F Y') }}</p>
                <p style="line-height: 1.5px; text-align:right; padding-right:114px;">Kepada</p>
                <p style="line-height: 1.5px; text-align:right; padding-left:500px;">Yth. Direktur UPTD BLUD RSUD </p>
                <p style="line-height: 1.5px; text-align:right; padding-right:21px;">Kabupaten Lombok Utara</p>
                <p style="line-height: 1.5px; text-align:right; padding-right:135px;">di-</p>
                <p style="line-height: 1.5px; text-align:right; padding-right:100px;">Tempat</p>
            </div>

            <div id="lampiran" class="col-md-6" style="line-height: 1.5;">
                <span style="display: inline-block; width: 80px;">Nomor</span>: {{$record->code}}<br />
                <span style="display: inline-block; width: 80px;">Lampiran</span>: - <br />
                <span style="display: inline-block; width: 80px;">Perihal</span>: {{$record->regarding}}
            </div>

            <div id="text-body" style="margin-top: 20px; line-height: 1.5; text-align:justify;" >
                <p>Dengan hormat,<br>Dalam rangka meningkatkan kualitas mutu pelayanan di ruang {{ $record->struct->name }}, kami mengajukan permintaan kebutuhan guna melengkapi sarana dan prasarana ruang {{ $record->struct->name }}. Adapun daftar kebutuhan aset yang kami ajukan terlampir.
                Demikian surat pengajuan ini kami buat, besar harapan agar dapat ditindak lanjuti dan direalisasikan, atas perhatiannya kami sampaikan terima kasih.</p></p>
                {{-- Lorem, ipsum dolor sit amet consectetur adipisicing elit. Repudiandae ut harum ipsam modi quia tempore eum asperiores hic. Molestiae neque totam excepturi molestias ex fuga provident tenetur placeat? Hic, nesciunt. --}}
            </div>
        </div>

        @php
            $user_pos = auth()->user()->position->location_id;
            $menyetujui_loc = \App\Models\Master\Org\OrgStruct::where('id',$user_pos)->value('parent_id');
            $menyetujui =  \App\Models\Master\Org\OrgStruct::where('id',$menyetujui_loc)->value('name');
            $loc = \App\Models\Master\Org\Position::where('location_id',$menyetujui_loc)->where('level','kepala')->value('id');
            $user_menyetujui = \App\Models\Auth\User::where('position_id',$loc)->value('name');
        @endphp

        <div class = "footer-surat">
            <div class="row" style="line-height: 1.5; text-align: center; margin-top:100px;">
                {{-- <div class="col-md-4" style="margin-top: 10px; float: right; padding-right: 100px;">
                    <p>Mengetahui <br/> Koordinator Dokter IGD</p>
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div> --}}
                
                <div class="col-md-6" style="margin-top: 10px; float: left; padding-left: 100px;">
                    <p>Yang Mengusulkan <br/> {{auth()->user()->position->name}}</p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
                <div class="col-md-6" style="margin-top: 10px; float: right; padding-right: 100px;">
                    <p>Menyetujui <br/> {{$menyetujui}}</p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">{{$user_menyetujui}}<br /> NIP. 196703221995031001</p>
                </div>
            </div>
            <div class="col-md-12" style="margin-top: 150px; text-align:center;">
                <p>Menyetujui <br/> {{$menyetujui}}</p>
                {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                <p style="margin-top: 75px;">{{$user_menyetujui}}<br /> NIP. 196703221995031001</p>
            </div>

            {{-- <div class="row" style="line-height: 1.5; text-align: center;">
            </div> --}}

        </div>

        </div>
    </div>



</body>
</html>