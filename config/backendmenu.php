<?php

return [
    [
        'section' => 'NAVIGASI',
        'name' => 'navigasi',
        'perms' => 'dashboard',
    ],
    // Dashboard
    [
        'name' => 'dashboard',
        'perms' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => 'fa fa-th-large',
        'url' => '/home',
    ],
    // PENGAJUAN
    [
        'name' => 'pengajuan',
        'title' => 'Pengajuan',
        'icon' => 'fa fa-road',
        'submenu' => [
            [
                'name' => 'pengajuan_pembelian-aset',
                'perms' => 'pengajuan.pembelian-aset',
                'title' => 'Pembelian',
                'url' => '/pengajuan/pembelian-aset',
            ],
            [
                'name' => 'pengajuan_berita-acara',
                'perms' => 'pengajuan.berita-acara',
                'title' => 'Berita Acara',
                'url' => '/pengajuan/berita-acara',
            ],
            [
                'name' => 'pengajuan_registrasi-aset',
                'perms' => 'pengajuan.registrasi-aset',
                'title' => 'Registrasi Aset',
                'url' => '/pengajuan/registrasi-aset',
            ],
        ]
    ],
    [
        'section' => 'ADMIN KONSOL',
        'name' => 'console_admin',
    ],
    [
        'name' => 'master',
        'perms' => 'master',
        'title' => 'Data Master',
        'icon' => 'fa fa-database',
        'submenu' => [
            [
                'name' => 'master_org',
                'title' => 'Struktur Organisasi',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master_org_root',
                        'title' => 'Root',
                        'url' => '/master/org/root'
                    ],
                    [
                        'name' => 'master_org_bod',
                        'title' => 'Direksi',
                        'url' => '/master/org/bod',
                    ],
                    [
                        'name' => 'master_org_department',
                        'title' => 'Departemen',
                        'url' => '/master/org/department',
                    ],
                    [
                        'name' => 'master_org_subdepartment',
                        'title' => 'Sub Departemen',
                        'url' => '/master/org/subdepartment',
                    ],
                    [
                        'name' => 'master_org_subsection',
                        'title' => 'Sub Unit Departemen',
                        'url' => '/master/org/subsection',
                    ],
                    [
                        'name' => 'master_org_position',
                        'title' => 'Jabatan',
                        'url' => '/master/org/position',
                    ],

                ]
            ],
            [
                'name' => 'Geografis',
                'title' => 'Geografis',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master_province',
                        'title' => 'Provinsi',
                        'url' => '/master/geografis/province'
                    ],
                    [
                        'name' => 'master_city',
                        'title' => 'Kota / Kabupaten',
                        'url' => '/master/geografis/city'
                    ],
                ]
            ],
            [
                'name' => 'master.Coa',
                'title' => 'Chart of Accounts',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master_coa_tanah',
                        'title' => 'Coa Tanah',
                        'url' => '/master/coa/tanah'
                    ],
                    [
                        'name' => 'master_coa_peralatan',
                        'title' => 'Coa Peralatan Mesin',
                        'url' => '/master/coa/peralatan-mesin'
                    ],
                    [
                        'name' => 'master_coa_bangunan',
                        'title' => 'Coa Gedung Bangunan',
                        'url' => '/master/coa/gedung-bangunan'
                    ],
                    [
                        'name' => 'master_coa_aset_lainya',
                        'title' => 'Coa Aset Tetap Lainya',
                        'url' => '/master/coa/aset-tetap-lainya'
                    ],
                    [
                        'name' => 'master_coa_jalan_irigasi',
                        'title' => 'Coa Jalan Irigasi Jaringan ',
                        'url' => '/master/coa/jalan-irigasi-jaringan'
                    ],
                    [
                        'name' => 'master_coa_kontruksi_pembangunan',
                        'title' => 'Coa Kontruksi Pembangunan',
                        'url' => '/master/coa/kontruksi-pembangunan'
                    ],
                ]
            ],
            [
                'name' => 'Vendor',
                'title' => 'Vendor',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master.type-vendor',
                        'title' => 'Jenis',
                        'url' => '/master/type-vendor',
                    ],
                    [
                        'name' => 'master.vendor',
                        'title' => 'Vendor',
                        'url' => '/master/vendor',
                    ],
                ]
            ],
        ]
    ],
    [
        'name' => 'setting',
        'perms' => 'setting',
        'title' => 'Pengaturan Umum',
        'icon' => 'fa fa-cogs',
        'submenu' => [
            [
                'name' => 'setting_role',
                'title' => 'Hak Akses',
                'url' => '/setting/role',
            ],
            [
                'name' => 'setting_flow',
                'title' => 'Flow Approval',
                'url' => '/setting/flow',
            ],
            [
                'name' => 'setting_user',
                'title' => 'Manajemen User',
                'url' => '/setting/user',
            ],
            [
                'name' => 'setting_activity',
                'title' => 'Audit Trail',
                'url' => '/setting/activity',
            ],
        ]
    ],
];
