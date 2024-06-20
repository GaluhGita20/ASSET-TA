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
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0px;
        }

        #logo {
            width: 120px;
            justify-content: center;
            /* margin-top: 20px; */
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
            font-family: Arial, sans-serif;
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

        #judul th, #judul td {
            border: 0px solid #ddd;
        }

        #lampiran th, #lampiran td {
            border: 0px solid #ddd;
            padding: 1px;
        }

        #tgl-beli th, #tgl-beli td {
            border: 0px solid #ddd;
            padding: 1px;
        }

        .border{
            border-bottom: 5px solid #000; 
        }

        .table1 {
            font-family: Arial, sans-serif;
            /* font-family: sans-serif; */
            color: #232323;
            border-collapse: collapse;
        }

        .table1, th, td {
            border: 1px solid #999;
            padding: 8px 8px;
        } 

        .isi{
            font-family: Arial, sans-serif;
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 12px;
            line-height: 1.5; 
            text-align:justify;
            
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
        @php
            use Carbon\Carbon;
        @endphp
        <div class="code" style="text-align: center; padding:10px;">
            <h3 style="border-bottom: 4px solid black; display: inline-block;">BERITA ACARA PENYERAHAN HASIL PEKERJAAN KEPADA KPA</h3>
            <p style="margin-top: -15px; font-size:14px;">Nomor : {{$record->no_spk.'/'.Carbon::parse($record->spk_start_date)->formatLocalized('%d/%B/%Y')}}</p>
        </div>
        
        <div class="isi">
            <span>Pada hari ini Rabu, Tanggal Enam, bulan Oktober tahun Dua Ribu Dua Puluh Satu, yang bertanda tangan di bawah ini</span>
            
            {{-- isi penerima --}}
            <div id="lampiran" class="col-md-6">
                <table style="margin-left:-3px;">
                    <tr>
                        <td width="50px;">1</td>
                        <td>Nama</td>
                        <td>:</td>
                        <td>Teguh</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>NIP</td>
                        <td>:</td>
                        <td>10001</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td rowspan="2">Kepala Bagian Pengadaan Barang dan Jasa Sekretariat Daerah Kabupaten Lombok
                        Utara selaku PPK pada UPTD RSUD Kabupaten Lombok Utara</td>
                    </tr>
                    <tr>   
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>Jalan Tioq Tata Tunaq Tanjung Lombok Utara
                        selanjutnya disebut sebagai PIHAK PERTAMA</td>
                    </tr>
                </table>
            </div>

            <div id="lampiran" class="col-md-6">
                <table style="margin-left:-3px;">
                    <tr>
                        <td width="50px;">2</td>
                        <td>Nama</td>
                        <td>:</td>
                        <td>Teguh</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>NIP</td>
                        <td>:</td>
                        <td>10001</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td rowspan="2">Kepala Bagian Pengadaan Barang dan Jasa Sekretariat Daerah Kabupaten Lombok
                        Utara selaku PPK pada UPTD RSUD Kabupaten Lombok Utara</td>
                    </tr>
                    <tr>   
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>Jalan Tioq Tata Tunaq Tanjung Lombok Utara
                        selanjutnya disebut sebagai PIHAK PERTAMA</td>
                    </tr>
                </table>
            </div>
            
            {{-- <div id="lampiran" class="col-md-6" style="line-height: 1.5; margin-top:20px;">
                <span style="display: inline-block; width: 80px;">2</span>
                <span style="display: inline-block; width: 80px; padding-left:7px;">Nama</span>: I Made Teguh Athana<br />
                <span style="display: inline-block; width: 80px; padding-left:90px;">NIP</span>: 1980000000000 <br />
                <span style="display: inline-block; width: 80px; padding-left:89px;">Jabatan</span>: Direktur UPTD RSUD Kabupaten Lombok Utara Selaku Kuasa Pengguna Anggaran 
                (KPA) UPTD RSUD Kabupaten 
                <span style="display: inline-block; width: 80px; padding-left:95px;"></span>Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:90px;">Alamat</span>: Jalan Tioq Tata Tunaq Tanjung Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:7px;"></span> selanjutnya disebut sebagai</span> <b>PIHAK KEDUA</b>
            </div> --}}
            {{-- isi penerima --}}
    
            <div style="line-height: 1.5; margin-top:10px;">
                <p>
                    Dengan ini <b>PIHAK PERTAMA</b> selaku PPK dan <b>PIHAK KEDUA</b> selaku KPA telah setuju dan sepakat untuk
                    melakukan Serah Terima Barang/Jasa atau Hasil Belanja Modal Peralatan dan Mesin - Alat Kedokteran dan Kesehatan;
                    Alat Kedokteran Bedah sesuai Kontrak Nomor : {{$record->no_spk}}, tanggal {{ \Carbon\Carbon::parse($record->spk_start_date)->format('j F Y')}}, yang diselenggarakan pada :
                </p>
                <div id="tgl-beli" style="margin-top: -15px;">
                    <table style="margin-left:-3px;">
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{\Carbon\Carbon::parse($record->receipt_date)->format('j F Y')}}</td>
                        </tr>
                        <tr>
                            <td>Bertempat di</td>
                            <td>:</td>
                            <td>UPTD RSUD Kabupaten Lombok Utara</td>
                        </tr>
                    </table>
                </div>
                {{-- <p>Tanggal</p>: {{\Carbon\Carbon::parse($record->receipt_date)->format('j F Y')}}</br> --}}
                {{-- <p>Bertempat di</p>: {{$record->location_receipt}} --}}
            </div>
    
            @php
                if (!function_exists('angkaTerbilang')) {
                    function angkaTerbilang($angka)
                    {
                        $angka = abs((int) $angka);
                        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
                        
                        $terbilang = '';
    
                        if ($angka < 12) {
                            $terbilang = ' ' . $bilangan[$angka];
                        } elseif ($angka < 20) {
                            $terbilang = angkaTerbilang($angka - 10) . ' belas';
                        } elseif ($angka < 100) {
                            $terbilang = angkaTerbilang($angka / 10) . ' puluh' . angkaTerbilang($angka % 10);
                        } elseif ($angka < 200) {
                            $terbilang = ' seratus' . angkaTerbilang($angka - 100);
                        } elseif ($angka < 1000) {
                            $terbilang = angkaTerbilang($angka / 100) . ' ratus' . angkaTerbilang($angka % 100);
                        } elseif ($angka < 2000) {
                            $terbilang = ' seribu' . angkaTerbilang($angka - 1000);
                        } elseif ($angka < 1000000) {
                            $terbilang = angkaTerbilang($angka / 1000) . ' ribu' . angkaTerbilang($angka % 1000);
                        } elseif ($angka < 1000000000) {
                            $terbilang = angkaTerbilang($angka / 1000000) . ' juta' . angkaTerbilang($angka % 1000000);
                        } elseif ($angka < 1000000000000) {
                            $terbilang = angkaTerbilang($angka / 1000000000) . ' milyar' . angkaTerbilang(fmod($angka, 1000000000));
                        } elseif ($angka < 1000000000000000) {
                            $terbilang = angkaTerbilang($angka / 1000000000000) . ' trilyun' . angkaTerbilang(fmod($angka, 1000000000000));
                        } elseif ($angka < 1000000000000000000) {
                            $terbilang = angkaTerbilang($angka / 1000000000000000) . ' quadriliun' . angkaTerbilang(fmod($angka, 1000000000000000));
                        }
    
                        return $terbilang;
                    }
                }
            @endphp
    
            <div style="line-height: 1.5; margin-top:15px;">
                <span> Adapun yang di Serah Terimakan antara lain:</span>
                <table class="table1" style="width: 100%;" >
                    <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nama Aset</th>
                        <th style="text-align: center">Kuantitas</th>
                        <th style="text-align: center">Harga Satuan (Rupiah)</th>
                        <th style="text-align: center">Ongkos Kirim (Rupiah)</th>
                        <th style="text-align: center">Total Harga (Rupiah)</th>
                    </tr>
                    @php 
                        $i = 1;
                    @endphp
    
                    <tr>
                        <td style="width: 2px;" style="text-align: center">{{$i}}</td>
                        <td style="text-align: center">{{$detailData->asetd->name}}</td>
                        <td style="text-align: center">{{$record->qty}}</td>
                        <td style="text-align: center">{{(number_format($record->unit_cost, 0, ',', ','))}}</td>
                        <td style="text-align: center">{{(number_format($record->shiping_cost, 0, ',', ','))}}</td>
                        <td style="text-align: center">{{(number_format($record->total_cost, 0, ',', ','))}}</td>
                    </tr>
    
                    <tr>
                        <td colspan="5">Jumlah</td>
                        <td style="text-align: center">{{(number_format($record->total_cost, 0, ',', ','))}}</th>
                    </tr>
                    <tr>
                        @php
                            $terbilang = angkaTerbilang($record->total_cost)
                        @endphp
                        <td colspan="6" style="text-align: center"><b>Terbilang : {{ucwords($terbilang)}} Rupiah</b></td>
                        
                    </tr>
                    
                </table>
                <div style="line-height: 1.5; ">
                    <p><b>Barang / Jasa atau Hasil Pekerjaan yang di Serah Terimakan selanjutnya dicatat sebagai aset daerah, disimpan dan didistribusikan sesuai dengan kebutuhan pelayanan oleh Pengelola Barang/Bendahara Material UPTD RSUD Kabupaten Lombok Utara.</b></p></br>
                    {{-- <span><b>Demikian BERITA ACARA PENYERAHAN BARANG/JASA ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya</b></span></br> --}}
                </div>
    
                <div style="line-height: 1.5; margin-top:10px;">
                    {{-- <span><b>Barang/Jasa atau Hasil Pekerjaan yang di Serah Terimakan selanjutnya dicatat sebagai aset daerah, disimpan dan didistribusikan sesuai dengan kebutuhan pelayanan oleh Pengelola Barang/Bendahara Material UPTD RSUD Kabupaten Lombok Utara</b></span></br> --}}
                    <p>Demikian <b>BERITA ACARA PENYERAHAN BARANG/JASA</b> ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya</p></br>
                </div>

                <p style="text-align: center;">
                    Tanjung, {{ \Carbon\Carbon::parse($record->spk_start_date)->format('j F Y')}}
                </p>

                <div id="ttd">
                    <table>
                        <tr>
                            <td style="text-align: center;">PIHAK PERTAMA </br>Pejabat Pembuat Komitmen UPTD RSUD Kabupaten Lombok Utara</td>
                            <td style="text-align: center;">PIHAK KEDUA </br> Direktur UPTD RSUD Kabupaten Lombok Utara Selaku Kuasa Pengguna Anggaran (KPA)</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;" rowspan="8"><b>(dr. Ahmad Haerul Umam)</b><br>NIP. 19911125202321001</td>
                            <td style="text-align: center;" rowspan="8"><b>(Sri Adriani, Amd.Keb)</b><br>NIP. 19911125202321001</td>
                        </tr>
                    </table>
                </div>
        
                <div id="ttd">
                    <table>
                        <tr>
                            <td style="text-align: center;">Barang telah dicatat Bendahara Material UPTD RSUD Kabupaten Lombok Utara</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;" rowspan="8"><b>(dr. Ahmad Haerul Umam)</b><br>NIP. 19911125202321001</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>




