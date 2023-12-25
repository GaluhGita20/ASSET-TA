<?php

namespace Database\Seeders;

use App\Models\Globals\Menu;
use App\Models\Globals\MenuFlow;
use Illuminate\Database\Seeder;

class MenuFlowSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // RISK ASSESSMENT
            [
                'module'   => 'perencanaan-aset',
                'FLOWS'     => [
                    [
                        "role_id"   => 5,
                        "type"      => 1,
                        "type_position" => 1,
                        "position_id" => NULL
                    ],
                ],
            ],
            [
                'module'   => 'transaksi',
                'submenu'  => [
                    [
                        'module'   => 'transaksi_berita-acara',
                        'FLOWS'     => [
                            [
                                "role_id"   => 5,
                                "type"      => 1,
                                "type_position" => 1,
                                "position_id" => NULL
                            ],
                        ],
                    ]
                ],
            ],
        ];

        $this->generate($data);
    }

    public function generate($data)
    {
        ini_set("memory_limit", -1);
        $exists = [];
        $order = 1;
        foreach ($data as $row) {
            $menu = Menu::firstOrNew(['module' => $row['module']]);
            $menu->order = $order;
            $menu->save();
            $exists[] = $menu->id;
            $order++;
            if (!empty($row['submenu'])) {
                foreach ($row['submenu'] as $val) {
                    $submenu = $menu->child()->firstOrNew(['module' => $val['module']]);
                    $submenu->order = $order;
                    $submenu->save();
                    $exists[] = $submenu->id;
                    $order++;
                    if (isset($val['FLOWS'])) {
                        $submenu->flows()->delete();
                        $f = 1;
                        foreach ($val['FLOWS'] as $key => $flow) {
                            $record = MenuFlow::firstOrNew([
                                'menu_id'   => $submenu->id,
                                'role_id'   => $flow['role_id'],
                                'type'      => $flow['type'],
                                'type_position'      => $flow['type_position'],
                                'position_id'      => $flow['position_id'],
                                'order'     => $f++,
                            ]);
                            $record->save();
                        }
                    }
                }
            }
            if (isset($row['FLOWS'])) {
                $menu->flows()->delete();
                $f = 1;
                foreach ($row['FLOWS'] as $key => $flow) {
                    $record = MenuFlow::firstOrNew([
                        'menu_id'   => $menu->id,
                        'role_id'   => $flow['role_id'],
                        'type'      => $flow['type'],
                        'type_position'      => $flow['type_position'],
                        'position_id'      => $flow['position_id'],
                        'order'     => $f++,
                    ]);
                    $record->save();
                }
            }
        }
        Menu::whereNotIn('id', $exists)->delete();
    }

    public function countActions($data)
    {
        $count = 0;
        foreach ($data as $row) {
            $count++;
            if (!empty($row['submenu'])) {
                $count += count($row['submenu']);
            }
        }
        return $count;
    }
}
