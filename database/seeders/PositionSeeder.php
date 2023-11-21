<?php

namespace Database\Seeders;

use App\Models\Master\Org\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $position = [
            [
                "location_id" => 2,
                "name" => "Kepala Direktur",
                "code" => 1001,
                "level" => "bod",
            ],
            [
                "location_id" => 2,
                "name" => "Wakil Kepala Direktur",
                "code" => 1002,
                "level" => "bod",
            ],
            [
                "location_id" => 3,
                "name" => "Kepala Bidang Penunjang Medik dan Non Medik",
                "code" => 1003,
                "level" => "departement",
            ],
            [
                "location_id" => 4,
                "name" => "Kepala Bidang Pelayanan Medik dan Keperawatan",
                "code" => 1004,
                "level" => "departement",
            ],
            [
                "location_id" => 6,
                "name" => "Kepala Bidang Pengembangan Sumber Daya Manusia",
                "code" => 1005,
                "level" => "departement",
            ],
            [
                "location_id" => 5,
                "name" => "Kepala Bagian Tata Usaha",
                "code" => 1006,
                "level" => "departement",
            ],
            [
                "location_id" => 7,
                "name" => "Kepala Seksi Penunjang Medik dan Non Medik",
                "code" => 1007,
                "level" => "departement",
            ],
            [
                "location_id" => 8,
                "name" => "Kepala Seksi Sarana dan Prasarana Logistik",
                "code" => 1008,
                "level" => "departement",
            ],
            [
                "location_id" => 9,
                "name" => "Kepala Seksi Pelayanan Medik",
                "code" => 1009,
                "level" => "departement",
            ],
            [
                "location_id" => 10,
                "name" => "Kepala Seksi Pelayanan Keperawatan",
                "code" => 1010,
                "level" => "departement",
            ],
            [
                "location_id" => 11,
                "name" => "Kepala Sub Bagian Program Perencanaan",
                "code" => 1011,
                "level" => "departement",
            ],
            [
                "location_id" => 12,
                "name" => "Kepala Sub Bagian Keuangan",
                "code" => 1012,
                "level" => "departement",
            ],
            [
                "location_id" => 13,
                "name" => "Kepala Sub Bagian Umum dan Kepegawaian",
                "code" => 1013,
                "level" => "departement",
            ],
            [
                "location_id" => 7,
                "name" => "Kepala Seksi Penunjang Medik dan Non Medik",
                "code" => 1014,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 7,
                "name" => "Kepala Seksi Penunjang Medik dan Non Medik",
                "code" => 1015,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 8,
                "name" => "Kepala Seksi Sarana dan Prasarana Logistik",
                "code" => 1016,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 9,
                "name" => "Kepala Seksi Pelayanan Medik",
                "code" => 1017,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 10,
                "name" => "Kepala Seksi Pelayanan Keperawatan",
                "code" => 1018,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 11,
                "name" => "Kepala Sub Bagian Program Perencanaan",
                "code" => 1019,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 12,
                "name" => "Kepala Sub Bagian Keuangan",
                "code" => 1020,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 13,
                "name" => "Kepala Sub Bagian Umum dan Kepegawaian",
                "code" => 1021,
                "level" => "subdepartement",
            ],

            [
                "location_id" => 7,
                "name" => "Staf Seksi Penunjang Medik dan Non Medik",
                "code" => 1022,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 7,
                "name" => "Staf Seksi Penunjang Medik dan Non Medik",
                "code" => 1023,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 8,
                "name" => "Staf Seksi Sarana dan Prasarana Logistik",
                "code" => 1024,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 9,
                "name" => "Staf Seksi Pelayanan Medik",
                "code" => 1025,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 10,
                "name" => "Staf Seksi Pelayanan Keperawatan",
                "code" => 1026,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 11,
                "name" => "Staf Sub Bagian Program Perencanaan",
                "code" => 1027,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 12,
                "name" => "Staf Sub Bagian Keuangan",
                "code" => 1028,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 13,
                "name" => "Staf Sub Bagian Umum dan Kepegawaian",
                "code" => 1029,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 17,
                "name" => "Kepala IPSRS",
                "code" => 1030,
                "level" => "subdepartement",
            ],
            [
                "location_id" => 18,
                "name" => "Kepala IGD",
                "code" => 1031,
                "level" => "subdepartement",
            ],
        ];

        $this->generate($position);
    }

    public function generate($position)
    {
        ini_set("memory_limit", -1);

        foreach ($position as $val) {
            $position              = Position::firstOrNew(['code' => $val['code']]);
            $position->location_id = $val['location_id'] ?? NULL;
            $position->name        = $val['name'];
            $position->code        = $val['code'];
            $position->level        = $val['level'] ?? NULL;
            $position->created_by = 1;
            $position->created_at  = \Carbon\Carbon::now();;
            $position->save();
        }
    }

    public function countActions($data)
    {
        $count = count($data);

        return $count;
    }
}
