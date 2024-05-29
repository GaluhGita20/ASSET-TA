<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @php
    $inventaris = $records;
    $groupedInventaris = [];
    $i =0;

    // Menggabungkan jumlah barang yang sama
    foreach ($records as $item) {
        $namaBarang = $item->usulans->asetd->name;
        $tahunBeliAwal = $item->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $item->usulans->trans->receipt_date->format('Y');

        if (isset($groupedInventaris[$namaBarang])) {
            $groupedInventaris[$namaBarang]['jumlah'] += 1;
            if(isset($groupedInventaris[$tahunBeliAwal])){
                $groupedInventaris[$tahunBeliAwal]['tahun_awal'] = $tahunBeliAwal;
            }// }elseif ($groupedInventaris[$tahunBeliAwal] < $tahunBeliAwal) {
            //     $groupedInventaris[$tahunBeliAwal]['tahun_akhir'] = $tahunBeliAwal;
            // }
        } else {
            $groupedInventaris[$namaBarang] = [
                'nama' => $namaBarang,
                'jumlah' => 1,
                'tahun_awal' => $tahunBeliAwal,
                'tahun_akhir' => $tahunBeliAwal,
            ];
        }
    }


    // Mengubah array asosiatif menjadi array numerik untuk di-loop di view
    $groupedInventaris = array_values($groupedInventaris);
    @endphp

    {{dd($groupedInventaris)}}
</body>
</html>