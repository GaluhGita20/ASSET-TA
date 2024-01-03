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
            
            /** REPORT **/
            [
                'name'          => 'report',
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
                    'perencanaan-aset'             => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view', 'create', 'edit', 'delete', 'approve'],
                    'registrasi.inventaris-aset'   => ['view', 'create', 'edit'],
                    // 'transaksi.pengadaan-aset'     => ['view', 'create', 'edit', 'approve'],
                    'master'                       => ['view', 'create', 'edit', 'delete'],
                    'setting'                      => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Direksi',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view', 'create', 'edit', 'delete', 'approve'],
                    'registrasi.inventaris-aset'   => ['view', 'create', 'edit'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Sub Bagian Program Perencanaan',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view', 'create', 'edit', 'delete', 'approve'],
                    'registrasi.inventaris-aset'   => ['view', 'create', 'edit'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Keuangan',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    'transaksi.pengadaan-aset'     => ['view', 'create', 'edit', 'delete', 'approve'],
                    'registrasi.inventaris-aset'   => ['view', 'create', 'edit'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Umum',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    // 'pengajuan.berita-acara'    => ['view', 'create', 'edit', 'approve'],
                    // 'pengajuan.registrasi-aset' => ['view', 'create', 'edit', 'approve'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'Sarpras',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    // 'pengajuan.berita-acara'    => ['view', 'create', 'edit', 'approve'],
                    // 'pengajuan.registrasi-aset' => ['view', 'create', 'edit', 'approve'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'BPKAD',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'       => ['view', 'create', 'edit', 'delete', 'approve'],
                    // 'pengajuan.berita-acara'    => ['view', 'create', 'edit', 'approve'],
                    // 'pengajuan.registrasi-aset' => ['view', 'create', 'edit', 'approve'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
                ],
            ],
            [
                'name'  => 'PPK',
                'PERMISSIONS'   => [
                    'dashboard'                 => ['view'],
                    'perencanaan-aset'  => ['view', 'create', 'edit', 'delete', 'approve'],
                    // 'pengajuan.penerimaan-aset'   => ['view', 'create', 'edit', 'delete'],
                    // 'pengajuan.berita-acara'    => ['view', 'create', 'edit', 'approve'],
                    // 'pengajuan.registrasi-aset' => ['view', 'create', 'edit', 'approve'],
                    'master'                    => ['view', 'create', 'edit', 'delete'],
                    'setting'                   => ['view', 'create', 'edit', 'delete'],
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
