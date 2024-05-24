<?php

namespace Database\Seeders;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            /** Example **/
            // [
            //     'name'          => 'settings.reportex',
            //     'action'        => ['view', 'create', 'edit', 'approve'],
            // ],
            /** DASHBOARD **/
            [
                'name'          => 'dashboard',
                'action'        => ['view'],
            ],

            /** MONITORING **/
            [
                'name'          => 'monitoring',
                'action'        => ['view'],
            ],

            /** PENGAJUAN**/
            [
                'name'          => 'perencanaan-aset',
                'action'        => ['view', 'create', 'edit', 'delete', 'approve'],
            ],
            [
                'name'          => 'transaksi.pengadaan-aset',
                'action'        => ['view', 'create', 'edit', 'delete', 'approve'],
            ],

            [
                'name'          => 'registrasi.inventaris-aset',
                'action'        => ['view', 'create', 'edit'],
            ],

            [
                'name'          => 'pemeliharaan-aset',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            [
                'name'          => 'perbaikan-aset',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            [
                'name'          => 'usulan_pembelian-sperpat',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            [
                'name'          => 'trans-sperpat',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            [
                'name'          => 'penghapusan-aset',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            [
                'name'          => 'pemutihan-aset',
                'action'        => ['view', 'create', 'edit','delete','approve'],
            ],

            
            /** REPORT **/
            [
                'name'          => 'report-perencanaan',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-transaksi',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-inventaris',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-pemeliharaan',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-perbaikan',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-penghapusan',
                'action'        => ['view'],
            ],

            [
                'name'          => 'report-pemutihan',
                'action'        => ['view'],
            ],

            /** ADMIN CONSOLE **/
            [
                'name'          => 'master',
                'action'        => ['view', 'create', 'edit', 'delete'],
            ],
            [
                'name'          => 'setting',
                'action'        => ['view', 'create', 'edit', 'delete'],
            ],
        ];

        $this->generate($permissions);

        $ROLES = [
            [
                'name'  => 'Administrator',
                'PERMISSIONS'   => [
                    'dashboard'                    => ['view'],
                    'master'                       => ['view', 'create', 'edit', 'delete'],
                    'setting'                      => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                ],
            ],
            [
                'name'  => 'Direksi',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view','approve'],
                    'transaksi.pengadaan-aset'     => ['view','approve'],
                    'registrasi.inventaris-aset'   => ['view'],
                    'pemeliharaan-aset'            => ['view'],
                    'perbaikan-aset'               => ['view'],
                    'usulan_pembelian-sperpat'     => ['view','approve'],
                    'trans-sperpat'                => ['view','approve'],
                    'penghapusan-aset'             => ['view','edit','delete'],
                    'pemutihan-aset'               => ['view'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                ],
            ],
            [
                'name'  => 'Sub Bagian Program Perencanaan',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view','approve'],
                    'registrasi.inventaris-aset'   => ['view','edit','delete'],
                    'pemeliharaan-aset'            => ['view'],
                    'perbaikan-aset'               => ['view','edit','delete'],
                    'usulan_pembelian-sperpat'     => ['view','approve'],
                    // 'trans-sperpat'                => ['view','approve'],
                    'penghapusan-aset'             => ['view','edit','delete'],
                    'pemutihan-aset'               => ['view'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                    //'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Keuangan',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view','approve'],
                    'registrasi.inventaris-aset'   => ['view','edit','delete'],
                    'pemeliharaan-aset'            => ['view'],
                    'perbaikan-aset'               => ['view','edit','delete'],
                    'trans-sperpat'                => ['view','approve'],
                    'penghapusan-aset'             => ['view','edit','delete'],
                    'pemutihan-aset'               => ['view'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                  //  'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Umum',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'registrasi.inventaris-aset'   => ['view','create','edit','delete'],
                    'pemeliharaan-aset'            => ['view'],
                    'perbaikan-aset'               => ['view','edit','delete'],
                    'penghapusan-aset'             => ['view','edit','delete'],
                    'pemutihan-aset'               => ['view'],
                    'report-perencanaan'           => ['view'],
                    // 'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    // 'report-perbaikan'             => ['view'],
                    // 'report-pemeliharaan'          => ['view'],
                    // 'report-penghapusan'           => ['view'],
                    // 'report-pemutihan'             => ['view'],
                ],
            ],
            [
                'name'  => 'Sarpras',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view'],
                    'registrasi.inventaris-aset'   => ['view','create','edit','delete'],
                    'pemeliharaan-aset'            => ['view','create','edit','delete','approve'],
                    'perbaikan-aset'               => ['view','create','edit','delete','approve'],
                    'usulan-sperpat'               => ['view','create','edit','delete'],
                    'trans-sperpat'                => ['view','create','edit','delete'],
                    'penghapusan-aset'             => ['view','create','edit','delete','approve'],
                    'pemutihan-aset'               => ['view','create','edit','delete','approve'],
                    'master'                       => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                ],
            ],
            [
                'name'  => 'BPKAD',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view'],
                    'transaksi.pengadaan-aset'     => ['view'],
                    'registrasi.inventaris-aset'   => ['view'],
                    'pemeliharaan-aset'            => ['view'],
                    'perbaikan-aset'               => ['view'],
                    'penghapusan-aset'             => ['view','edit','delete','approve'],
                    'pemutihan-aset'               => ['view','edit','delete'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                ],
            ],
            [
                'name'  => 'PPK',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'  => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view','create','edit','delete'],
                    'registrasi.inventaris-aset'   => ['view'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'report-perencanaan'           => ['view'],
                    'report-transaksi'             => ['view'],
                    'report-inventaris'            => ['view'],
                    'report-perbaikan'             => ['view'],
                    'report-pemeliharaan'          => ['view'],
                    'report-penghapusan'           => ['view'],
                    'report-pemutihan'             => ['view'],
                ],
            ],
        ];
        foreach ($ROLES as $role) {
            $record = Role::firstOrNew(['name' => $role['name']]);
            $record->name = $role['name'];
            $record->save();
            $perms = [];
            foreach ($role['PERMISSIONS'] as $module => $actions) {
                foreach ($actions as $action) {
                    $perms[] = $module . '.' . $action;
                }
            }
            $perm_ids = Permission::whereIn('name', $perms)->pluck('id');
            // dd($perm_ids);
            $record->syncPermissions($perm_ids);
        }
    }

    public function generate($permissions)
    {
        // Role
        $admin = Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Administrator',
            ]
        );

        $perms_ids = [];
        foreach ($permissions as $row) {
            foreach ($row['action'] as $key => $val) {
                $name = $row['name'] . '.' . trim($val);
                $perms = Permission::firstOrCreate(compact('name'));
                $perms_ids[] = $perms->id;
                if (!$admin->hasPermissionTo($perms->name)) {
                    if ($name == 'monitoring.view') continue;
                    $admin->givePermissionTo($perms);
                }
            }
        }
        Permission::whereNotIn('id', $perms_ids)->delete();

        // Clear Perms Cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function countActions($data)
    {
        $count = 0;
        foreach ($data as $row) {
            $count += count($row['action']);
        }

        return $count;
    }
}
