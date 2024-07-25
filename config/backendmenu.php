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
        'name' => 'perencanaan',
        'title' => 'Pengajuan Perencanaan',
        'icon' => 'fa fa-road',
        'submenu' => [
            [
                'name' => 'perencanaan-aset',
                'perms' => 'perencanaan-aset',
                'title' => 'Unit Penunjang',
                'url' => '/pengajuan/perencanaan-aset',
            ],
            [
                'name' => 'perencanaan-aset-pelayanan',
                'perms' => 'perencanaan-aset-pelayanan',
                'title' => 'Unit Umum',
                'url' => '/pengajuan/perencanaan-aset-pelayanan',
            ],
        ],
    ],

    // fitur belum jadi ===========================================================
    [
        'name' => 'perubahan',
        'title' => 'Perubahan Perencanaan',
        'icon' => 'fa fa-file-alt',
        'submenu' =>[
            [
                'name' => 'perubahan-perencanaan',
                'perms' => 'perubahan-perencanaan',
                'title' => 'Unit Penunjang',
                'url' => '/pengajuan/perubahan-perencanaan-aset',
            ],
            [
                'name' => 'perubahan-usulan-umum',
                'perms' => 'perubahan-usulan-umum',
                'title' => 'Unit Umum',
                'url' => '/pengajuan/perubahan-usulan-umum',
            ],
        ],
        
    ],
    // fitur belum jadi ===========================================================

    // [
    //     'name' => 'pembelian-aset',
    //     'title' => 'Transaksi Pembelian',
    //     'perms' => 'transaksi.pembelian-aset',
    //     'icon' => 'fa fa-road',
    //     'url' => '/transaksi/pembelian-aset',
    // ],
    [
        'name' => 'transaksi',
        'title' => 'BAST Aset',
        'icon' => 'fa fa-money-bill',
        'submenu' => [
            [
                'name' => 'transaksi_waiting-purchase',
                'perms' => 'transaksi.pengadaan-aset',
                'title' => 'Usulan Pembelian',
                'url' => '/transaksi/waiting-purchase',
            ],
            [
                'name' => 'transaksi_pengadaan-aset',
                'perms' => 'transaksi.pengadaan-aset',
                'title' => 'Penerimaan Pembelian',
                'url' => '/transaksi/pengadaan-aset',
            ],
            [
                'name' => 'transaksi_non-pengadaan-aset',
                'perms' => 'transaksi.pengadaan-aset',
                'title' => 'Penerimaan Hibah',
                'url' => '/transaksi/non-pengadaan-aset',
            ],
        ]
    ],
    [
        'name' => 'inventaris',
        'title' => 'Aset',
        'icon' => 'fa fa-cube',
        'submenu' => [
            [
                'name' => 'inventaris',
                'title' => 'Inventaris Aset',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/inventaris-aset',
            ],
            [
                'name' => 'inventaris_kib-a',
                'title' => 'Aset KIB A',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-a',
            ],
            [
                'name' => 'inventaris_kib-b',
                'title' => 'Aset KIB B',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-b',
            ],
            
            [
                'name' => 'inventaris_kib-c',
                'title' => 'Aset KIB C',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-c',
            ],
            [
                'name' => 'inventaris_kib-d',
                'title' => 'Aset KIB D',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-d',
            ],
            [
                'name' => 'inventaris_kib-e',
                'title' => 'Aset KIB E',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-e',
            ],
            
            [
                'name' => 'inventaris_kib-f',
                'title' => 'Aset KIB F',
                'perms' => 'registrasi.inventaris-aset',
                'url' => '/inventaris/kib-f',
            ],
        ]
    ],

    [
        'name' => 'pemeliharaan-aset',
        'title' => 'Pemeliharaan Aset',
        'perms' => 'pemeliharaan-aset',
        'icon' => 'fa fa-toolbox',
        'url' => 'pemeliharaan/pemeliharaan-aset',
    ],

    [
        'name' => 'perbaikan',
        'title' => 'Pengajuan Perbaikan',
        'icon' => 'fa fa-hammer',
        'submenu' => [
            [
                'name' => 'perbaikan-aset',
                'perms' => 'perbaikan-aset',
                'title' => 'Usulan Perbaikan',
                'url' => '/perbaikan/perbaikan-aset',
            ],
            [
                'name' => 'usulan_pembelian-sperpat',
                'perms' => 'usulan_pembelian-sperpat',
                'title' => 'Usulan Sperpat',
                'url' => '/perbaikan/usulan-sperpat',
            ],
            [
                'name' => 'trans-sperpat',
                'perms' => 'trans-sperpat',
                'title' => 'Transaksi Sperpat Aset',
                'url' => '/perbaikan/trans-sperpat',
            ],
            [
                'name' => 'pj-perbaikan-aset',
                'perms' => 'perbaikan-aset',
                'title' => 'Hasil Akhir Perbaikan',
                'url' => '/perbaikan/pj-perbaikan',
            ],
        ]
    ],

    // [
    //     'name' => 'perbaikan-aset',
    //     'title' => 'Pengajuan Perbaikan',
    //     'perms' => 'perbaikan-aset',
    //     'icon' => 'fa fa-road',
    //     'url' => '/pengajuan/perbaikan-aset',
    // ],

    [
        'name' => 'penghapusan-aset',
        'title' => 'Pengajuan Penghapusan',
        'perms' => 'penghapusan-aset',
        'icon' => 'fa fa-trash',
        'url' => '/pengajuan/penghapusan-aset',
    ],

    [
        'name' => 'pemutihan-aset',
        'title' => 'Pemutihan Aset',
        'perms' => 'pemutihan-aset',
        'icon' => 'fa fa-coins',
        'url' => '/pengajuan/pemutihan-aset',
    ],

    [
        'name' => 'pelaporan',
        'title' => 'Pelaporan',
        'icon' => 'fa fa-book',
        'submenu' => [
            [
                'name' => 'laporan_perencanaan-aset',
                'perms' => 'report-perencanaan',
                'title' => 'Perencanaan Aset',
                'url' => '/laporan/perencanaan-aset',
            ],

            // [
            //     'name' => 'penerimaan',
            //     'title' => 'Transaksi Aset',
            //     // 'icon' => 'fa fa-cube',
            //     'submenu' => [
                    [
                        'name' => 'laporan_penerimaan-aset',
                        'perms' => 'report-transaksi',
                        'title' => 'Pembelian Aset',
                        'url' => '/laporan/penerimaan-aset',
                    ],

                    [
                        'name' => 'laporan_penerimaan-hibah-aset',
                        'perms' => 'report-transaksi',
                        'title' => 'Hibah Aset',
                        'url' => '/laporan/penerimaan-hibah-aset',
                    ],
            //     ],
            // ],

            // [
            //     'name' => 'inventaris',
            //     'title' => 'Inventaris Aset',
            //     // 'icon' => 'fa fa-cube',
            //     'submenu' => [
            // [
            //     'name' => 'laporan-inventaris_kib-a',
            //     'perms' => 'report-inventaris',
            //     // 'perms' => 'report-perencanaan',
            //     'title' => 'Daftar Laporan Aset',
            //     // 'url' => '/laporan/inventaris/kib-a',
                // 'submenu' => [
                    [
                        'name' => 'laporan-inventaris_kib-a',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB A',
                        'url' => '/laporan/inventaris/kib-a',
                    ],
                    [
                        'name' => 'laporan-inventaris_kib-b',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB B',
                        'url' => '/laporan/inventaris/kib-b',
                    ],
                    [
                        'name' => 'laporan-inventaris_kib-c',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB C',
                        'url' => '/laporan/inventaris/kib-c',
                    ],
                    [
                        'name' => 'laporan-inventaris_kib-d',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB D',
                        'url' => '/laporan/inventaris/kib-d',
                    ],
                    [
                        'name' => 'laporan-inventaris_kib-e',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB E',
                        'url' => '/laporan/inventaris/kib-e',
                    ],
                    [
                        'name' => 'laporan-inventaris_kib-f',
                        'perms' => 'report-inventaris',
                        'title' => 'Aset KIB F',
                        'url' => '/laporan/inventaris/kib-f',
                    // ],
                    ],
            //    ],
            // ],

            // [
            //     'name' => 'perbaikan',
            //     'title' => 'Perbaikan Aset',
            //     'perms' => 'report-perbaikan',
            //     // 'icon' => 'fa fa-cube',
            //     'submenu' => [

                    [
                        'name' => 'laporan_perbaikan-aset',
                        'perms' => 'report-perbaikan',
                        'title' => 'Perbaikan Aset',
                        'url' => '/laporan/perbaikan-aset',
                    ],

                    [
                        'name' => 'laporan_perbaikan-sperpat-aset',
                        'perms' => 'report-perbaikan',
                        'title' => 'Transaksi Sperpat Aset',
                        'url' => '/laporan/perbaikan-sperpat-aset',
                    ],
            //     ],
            // ],

            [
                'name' => 'laporan_pemeliharaan-aset',
                'perms' => 'report-pemeliharaan',
                'title' => 'Pemeliharaan Aset',
                'url' => '/laporan/pemeliharaan-aset',
            ],
            [
                'name' => 'laporan_penghapusan-aset',
                'perms' => 'report-penghapusan',
                'title' => 'Penghapusan Aset',
                'url' => '/laporan/penghapusan-aset',
            ],
            [
                'name' => 'laporan_pemutihan-aset',
                'perms' => 'report-pemutihan',
                'title' => 'Pemutihan Aset',
                'url' => '/laporan/pemutihan-aset',
            ],

            // [
            //     'name' => 'laporan_hibah-aset',
            //     'perms' => 'perencanaan-aset',
            //     'title' => 'Penerimaan Hibah Aset',
            //     'url' => '/laporan/hibah-aset',
            // ],

            // [
            //     'name' => 'laporan_pengadaan-aset',
            //     'perms' => 'perencanaan-aset',
            //     'title' => 'Pengadaan Aset',
            //     'url' => '/laporan/pengadaan-aset',
            // ],
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
                    // [
                    //     'name' => 'master_org_subsection',
                    //     'title' => 'Sub Unit Departemen',
                    //     'url' => '/master/org/subsection',
                    // ],
                    [
                        'name' => 'master_org_position',
                        'title' => 'Jabatan',
                        'url' => '/master/org/position',
                    ],

                ]
            ],
            // [
            //     'name' => 'Ruang',
            //     'title' => 'Ruang',
            //     'url' => '',
            //     'submenu' => [
            //         [
            //             'name' => 'master_location',
            //             'title' => 'Master Ruang',
            //             'url' => '/master/location'
            //         ],
            //     ],
            // ],
            [
                'name' => 'master_location',
                'title' => 'Ruang',
                'url' => '/master/location',
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
                    [
                        'name' => 'master_district',
                        'title' => 'Daerah',
                        'url' => '/master/geografis/district'
                    ],
                ]
            ],
            [
                'name' => 'master.Coa',
                'title' => 'Daftar Akun Aset',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master_coa_tanah',
                        'title' => 'Tanah',
                        'url' => '/master/coa/tanah'
                    ],
                    [
                        'name' => 'master_coa_peralatan',
                        'title' => 'Peralatan Mesin',
                        'url' => '/master/coa/peralatan-mesin'
                    ],
                    [
                        'name' => 'master_coa_bangunan',
                        'title' => 'Gedung Bangunan',
                        'url' => '/master/coa/gedung-bangunan'
                    ],
                    [
                        'name' => 'master_coa_aset_lainya',
                        'title' => 'Aset Tetap Lainya',
                        'url' => '/master/coa/aset-tetap-lainya'
                    ],
                    [
                        'name' => 'master_coa_jalan_irigasi',
                        'title' => 'Jalan Irigasi Jaringan ',
                        'url' => '/master/coa/jalan-irigasi-jaringan'
                    ],
                    [
                        'name' => 'master_coa_kontruksi_pembangunan',
                        'title' => 'Kontruksi Pembangunan',
                        'url' => '/master/coa/kontruksi-pembangunan'
                    ],
                ]
            ],

            [
                'name' => 'master_pengadaan',
                'title' => 'Jenis Pengadaan',
                'url' => '/master/data-pengadaan',
                // 'submenu' => [
                //     [
                //         'name' => 'master_pengadaan',
                //         'title' => 'Master Pengadaan',
                //         'url' => '/master/data-pengadaan'
                //     ],
                // ],
            ],
            [
                'name' => 'master_pemutihan',
                'title' => 'Jenis Pemutihan',
                'url' => '/master/data-pemutihan',
                // 'submenu' => [
                //     [
                //         'name' => 'master_pemutihan',
                //         'title' => 'Master Pemutihan',
                //         'url' => '/master/data-pemutihan'
                //     ],
                // ],
            ],
            [
                'name' => 'master_aset',
                'title' => 'Aset',
                'url' => '/master/data-aset',
                // 'submenu' => [
                //     [
                //         'name' => 'master_aset',
                //         'title' => 'Master Aset',
                //         'url' => '/master/data-aset'
                //     ],
                // ],
            ],
            [
                'name' => 'master_dana',
                'title' => 'Sumber Dana',
                'url' => '/master/dana',
            ],
            [
                'name' => 'master_hakTanah',
                'title' => 'Hak Tanah',
                'url' => '/master/hakTanah',
            ],
            [
                'name' => 'master_statusTanah',
                'title' => 'Status Tanah',
                'url' => '/master/statusTanah',
            ],
            [
                'name' => 'master_bahanAset',
                'title' => 'Bahan Aset',
                'url' => '/master/bahanAset',
            ],
            [
                'name' => 'Vendor',
                'title' => 'Vendor',
                'url' => '',
                'submenu' => [
                    [
                        'name' => 'master.type-vendor',
                        'title' => 'Jenis Usaha',
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
                'title' => 'Log Activity',
                'url' => '/setting/activity',
            ],
        ]
    ],
];
