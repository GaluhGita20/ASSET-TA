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

        body{font-family: Arial, Helvetica, sans-serif; font-size:12px;  background-color: #ccc}
        .rangkasurat{width:210mm; margin:0 auto; background-color: #fff; height:297mm; padding:20mm; }
        /* table{border-bottom: 5px solid #000; padding: 2px} */
        .tengah{text-align: center; line-height: 5px; }
        #tls{text-align: right; padding-right: 240px;}
        .alamat-tujuan{margin-left:50%;}

        .table1 {
            font-family: sans-serif;
            color: #232323;
            border-collapse: collapse;
        }

        .table1, th, td {
            border: 1px solid #999;
            padding: 8px 8px;
        } 

    </style>
</head>
<body>
    <div class="page1">
        <div class="rangkasurat">
            <table width="100%" style="border-bottom: 5px solid #000; padding: 2px">
                <tr>
                    <td style="border: 0px;"><img src="{{'/'.(config('base.logo.auth'))}}" width="140px"></td>
                    <td style="border: 0px;" class="tengah">
                        <h2>PEMERINTAH KABUPATEN LOMBOK UTARA</h2>
                        <h2>UPTD BLUD RUMAH SAKIT UMUM DAERAH</h2>
                        <h2>KABUPATEN LOMBOK UTARA</h2>
                        <h5 style="margin-top: -4px; margin-bottom: -4px; font-weight:100">Jln Raya Tiok Tata Tunaq Tanjung Kode Pos : 83352</h5>
                        <h5 style="top:-5px">Telp. (0370) 6123019 (fax. 90370) 6123019 e_mail : rsud_klu@yahoo.com</h5>
                    </td>
                    <td style="border: 0px;"><img src="{{'/'.(config('base.logo.auth'))}}" width="140px"></td>
                </tr>
            </table>
    

            <div class="code" style="text-align: center; margin-top:30px; margin-bottom:30px; padding: 10px;">
                <h2 style="border-bottom: 4px solid black; display: inline-block;">BERITA ACARA PENYERAHAN HASIL PEKERJAAN KEPADA KPA</h2>
                <h4 style="margin-top: -10px;">Nomor: </h4>
            </div>
            
            <span>Pada hari ini Rabu, Tanggal Enam, bulan Oktober tahun Dua Ribu Dua Puluh Satu, yang bertanda tangan di bawah ini</span>
            <div id="lampiran" class="col-md-6" style="line-height: 1.5; margin-top:3px;">
                <span style="display: inline-block; width: 80px;">1</span>
                <span style="display: inline-block; width: 80px; padding-left:7px;">Nama</span>: I Made Teguh Athana
                <br />
                <span style="display: inline-block; width: 80px; padding-left:90px;">NIP</span>:  1980000000000 <br />
                <span style="display: inline-block; width: 80px; padding-left:89px;">Jabatan</span>: Kepala Bagian Pengadaan Barang dan Jasa Sekretariat Daerah Kabupaten Lombok Utara selaku PPK pada UPTD 
                <span style="display: inline-block; width: 80px; padding-left:95px;"></span>RSUD Kabupaten Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:90px;">Alamat</span>: Jalan Tioq Tata Tunaq Tanjung Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:7px;"></span> selanjutnya disebut sebagai</span> <b>PIHAK PERTAMA</b>
            </div>
            
            <div id="lampiran" class="col-md-6" style="line-height: 1.5; margin-top:20px;">
                <span style="display: inline-block; width: 80px;">2</span>
                <span style="display: inline-block; width: 80px; padding-left:7px;">Nama</span>: I Made Teguh Athana<br />
                <span style="display: inline-block; width: 80px; padding-left:90px;">NIP</span>: 1980000000000 <br />
                <span style="display: inline-block; width: 80px; padding-left:89px;">Jabatan</span>: Direktur UPTD RSUD Kabupaten Lombok Utara Selaku Kuasa Pengguna Anggaran 
                (KPA) UPTD RSUD Kabupaten 
                <span style="display: inline-block; width: 80px; padding-left:95px;"></span>Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:90px;">Alamat</span>: Jalan Tioq Tata Tunaq Tanjung Lombok Utara <br/>
                <span style="display: inline-block; width: 80px; padding-left:7px;"></span> selanjutnya disebut sebagai</span> <b>PIHAK KEDUA</b>
            </div>

            <div style="line-height: 1.5; margin-top:20px;">
                <span>
                    Dengan ini <b>PIHAK PERTAMA</b> selaku PPK dan <b>PIHAK KEDUA</b> selaku KPA telah setuju dan sepakat untuk
                    melakukan Serah Terima Barang/Jasa atau Hasil Belanja Modal Peralatan dan Mesin - Alat Kedokteran dan Kesehatan;
                    Alat Kedokteran Bedah sesuai Kontrak Nomor : {{$record->no_spk}}, tanggal {{ \Carbon\Carbon::parse($record->spk_start_date)->format('j F Y')}}, yang diselenggarakan pada :
                </span></br>
                <span style="display: inline-block; width: 80px; ">Tanggal</span>: {{\Carbon\Carbon::parse($record->receipt_date)->format('j F Y')}}</br>
                <span style="display: inline-block; width: 80px; ">Bertempat di</span>: {{$record->location_receipt}}
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

            <div style="line-height: 1.5; margin-top:20px;">
                <span> Adapun yang di Serah Terimakan antara lain:</span>
                <table class="table1" style="width: 100%;" >
                    <tr>
                        <th>No</th>
                        <th>Nama Aset</th>
                        <th>Kuantitas</th>
                        <th>Harga Satuan (Rp)</th>
                        <th>Ongkos Kirim (Rp)</th>
                        <th>Total Harga (Rp)</th>
                    </tr>
                    @php 
                        $i = 1;
                    @endphp
 
                    <tr>
                        <td style="width: 2px;">{{$i}}</td>
                        <td>{{$detailData->asetd->name}}</td>
                        <td>{{$record->qty}}</td>
                        <td>{{(number_format($record->unit_cost, 0, ',', ','))}}</td>
                        <td>{{(number_format($record->shiping_cost, 0, ',', ','))}}</td>
                        <td>{{(number_format($record->total_cost, 0, ',', ','))}}</td>
                    </tr>

                    <tr>
                        <td colspan="5">Jumlah</td>
                        <td>{{(number_format($record->total_cost, 0, ',', ','))}}</th>
                    </tr>
                    <tr>
                        @php
                            $terbilang = angkaTerbilang($record->total_cost)
                        @endphp
                        <td colspan="6" style="text-align: center"><b>Terbilang : {{ucwords($terbilang)}} Rupiah</b></td>
                        
                    </tr>
                    
                </table>
                <div style="line-height: 1.5; margin-top:20px;">
                    <span><b>Barang/Jasa atau Hasil Pekerjaan yang di Serah Terimakan selanjutnya dicatat sebagai aset daerah, disimpan dan didistribusikan sesuai dengan kebutuhan pelayanan oleh Pengelola Barang/Bendahara Material UPTD RSUD Kabupaten Lombok Utara</b></span></br>
                    {{-- <span><b>Demikian BERITA ACARA PENYERAHAN BARANG/JASA ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya</b></span></br> --}}
                </div>

                <div style="line-height: 1.5; margin-top:20px;">
                    {{-- <span><b>Barang/Jasa atau Hasil Pekerjaan yang di Serah Terimakan selanjutnya dicatat sebagai aset daerah, disimpan dan didistribusikan sesuai dengan kebutuhan pelayanan oleh Pengelola Barang/Bendahara Material UPTD RSUD Kabupaten Lombok Utara</b></span></br> --}}
                    <span><b>Demikian BERITA ACARA PENYERAHAN BARANG/JASA ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya</b></span></br>
                </div>
            </div>

            <div style="line-height: 1.5; margin-top:20px; text-align:center;">
                <div class="col-md-4" style="float: right; ">
                    <p>PIHAK PERTAMA<br/>Pejabat Pembuat Komitmen UPTD RSUD
                    </br>Kabupaten Lombok Utara</p>
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
                
                <div class="col-md-4" style="float: left;">
                    <p>PIHAK KEDUA<br/> Direktur UPTD RSUD Kabupaten Lombok Utara 
                    <br/>selaku Kuasa Pengguna Anggaran (KPA)</p>
                    {{-- <p>Mengetahui <br/> Koordinator Dokter IGD</p> --}}
                    <p style="margin-top: 75px;">dr. Ahmad Haerul Umam <br /> NIP. 196703221995031001</p>
                </div>
            
            </div>
        </div>
    </div>



</body>
</html>