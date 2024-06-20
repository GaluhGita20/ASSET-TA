<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Disposisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
        }

        .header h1, .header h2, .header p {
            margin: 5px 0;
        }

        .content {
            margin-top: 20px;
        }

        .content p {
            text-align: justify;
            margin: 10px 0;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            text-align: center;
            width: 45%;
        }

        .signature div p {
            margin: 5px 0;
        }

        .date, .number {
            margin-bottom: 20px;
        }

        .number p {
            margin: 0;
        }

        .content p.indent {
            text-indent: 50px;
        }

        .content p.address {
            margin-left: 50px;
        }

        .disposisi {
            border: 2px solid black;
            padding: 10px;
        }

        .disposisi table {
            width: 100%;
            border-collapse: collapse;
        }

        .disposisi table, .disposisi th, .disposisi td {
            border: 1px solid black;
        }

        .disposisi th, .disposisi td {
            padding: 5px;
            text-align: left;
        }

        .disposisi .header {
            text-align: left;
            margin-bottom: 10px;
            border-bottom: none;
            padding-bottom: 0;
        }

        .disposisi .header img {
            float: left;
            margin-right: 10px;
        }

        .disposisi .header div {
            display: inline-block;
        }

        .disposisi .details, .disposisi .notes, .disposisi .footer {
            margin-top: 10px;
        }

        .disposisi .footer {
            display: flex;
            justify-content: space-between;
        }

        .disposisi .footer div {
            text-align: center;
            width: 30%;
        }

        .disposisi .footer div p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="disposisi">
        <div class="header">
            <img src="logo.png" alt="Logo" width="50" height="50">
            <div>
                <h1>PEMERINTAH KABUPATEN LOMBOK UTARA</h1>
                <h2>RUMAH SAKIT UMUM DAERAH</h2>
                <h3>KABUPATEN LOMBOK UTARA</h3>
                <p>Jl. Raya Tioq Tata Tunaq Tanjung Kode Pos: 83352</p>
                <p>Telp. (0370) 6123010 Fax. (0370) 6123010 e_mail: rsud_klu@yahoo.com</p>
            </div>
        </div>
        <h2 style="text-align: center;">LEMBAR DISPOSISI</h2>
        <div class="details">
            <table>
                <tr>
                    <th>Surat dari</th>
                    <td>:</td>
                    <td>P.H. Kepala Bid. Pelayanan Medik</td>
                    <th>Diterima Tgl</th>
                    <td>:</td>
                    <td>5 Oktober 2023</td>
                </tr>
                <tr>
                    <th>Nomor Surat</th>
                    <td>:</td>
                    <td>445.2/01/RSU KLU/XI/2023</td>
                    <th>No Agenda</th>
                    <td>:</td>
                    <td>773</td>
                </tr>
                <tr>
                    <th>Tanggal Surat</th>
                    <td>:</td>
                    <td>3 Oktober 2023</td>
                    <th>Sifat</th>
                    <td>:</td>
                    <td>
                        <input type="checkbox"> Biasa  
                        <input type="checkbox"> Segera  
                        <input type="checkbox"> Sangat Segera  
                        <input type="checkbox"> Rahasia  
                    </td>
                </tr>
                <tr>
                    <th>Perihal</th>
                    <td>:</td>
                    <td colspan="4">Pengajuan Permohonan Kebutuhan Sarana & Prasarana</td>
                </tr>
            </table>
        </div>
        <div class="notes">
            <table>
                <tr>
                    <th>Ditujukan Kepada Yth</th>
                    <td>:</td>
                    <td>
                        <input type="checkbox"> Kabag Tata Usaha<br>
                        <input type="checkbox"> Kabid Medik dan Keperawatan<br>
                        <input type="checkbox"> Kabid PSDM & Kehumasan  
                    </td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>:</td>
                    <td>Yth. Kabag Sarpras untuk di Medis Kabidok tgl 5 Okt<br> Biro Medik sangat di problem utk di Coroba<br> Mzzoning AM</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <div>
                <p>Direktur Rumah Sakit Umum Daerah</p>
                <p>Kabupaten Lombok Utara</p>
                <p>Paraf dan Tanda Tangan</p>
                <br><br><br>
                <p>drg. I Made Suasa</p>
                <p>NIP. 197003062006041007</p>
            </div>
            <div>
                <p>(dr. Ahmad Haerul Umam)</p>
                <p>NIP. 19911125202321001</p>
            </div>
            <div>
                <p>Mengetahui</p>
                <p>Kabid Medik dan Keperawatan</p>
                <br><br><br>
                <p>(dr. Encu Sukandi, M.Ked.Klin.Sp.MK)</p>
                <p>NIP. 197801302009011006</p>
            </div>
        </div>
    </div>
</body>
</html>