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
        'name' => 'perencanaan-aset',
        'title' => 'Pengajuan Perencanaan',
        'perms' => 'perencanaan-aset',
        'icon' => 'fa fa-road',
        'url' => '/pengajuan/perencanaan-aset',
        // 'submenu' => [
        //     [
        //         'name' => 'pengajuan_perencanaan-aset',
        //         'perms' => 'pengajuan.perencanaan-aset',
        //         'title' => 'Perencanaan',
        //         'url' => '/pengajuan/perencanaan-aset',
        //     ],
            
        //     [
        //         'name' => 'pengajuan_berita-acara',
        //         'perms' => 'pengajuan.berita-acara',
        //         'title' => 'Berita Acara',
        //         'url' => '/pengajuan/berita-acara',
        //     ],
        //     [
        //         'name' => 'pengajuan_registrasi-aset',
        //         'perms' => 'pengajuan.registrasi-aset',
        //         'title' => 'Registrasi Aset',
        //         'url' => '/pengajuan/registrasi-aset',
        //     ],
        // ]
    ],
    // [
    //     'name' => 'pembelian-aset',
    //     'title' => 'Transaksi Pembelian',
    //     'perms' => 'transaksi.pembelian-aset',
    //     'icon' => 'fa fa-road',
    //     'url' => '/transaksi/pembelian-aset',
    // ],
    [
        'name' => 'transaksi',
        'title' => 'Transaksi Aset',
        'icon' => 'fa fa-road',
        'submenu' => [
            [
                'name' => 'transaksi_waiting-purchase',
                'perms' => 'transaksi.waiting-purchase',
                'title' => 'Usulan Pengadaan',
                'url' => '/transaksi/waiting-purchase',
            ],
            [
                'name' => 'transaksi_pengadaan-aset',
                'perms' => 'transaksi.pengadaan-aset',
                'title' => 'Pengdadaan Aset',
                'url' => '/transaksi/pengadaan-aset',
            ],
            
            [
                'name' => 'transaksi_berita-acara',
                'perms' => 'transaksi.berita-acara',
                'title' => 'Penerimaan Aset',
                'url' => '/transaksi/berita-acara',
            ],
        ]
    ],
    // [
    //     'name' => 'registrasi_aset',
    //     'title' => 'Registrasi Aset',
    //     'icon' => 'fa fa-road',
    //     'submenu' => [
    //         [
    //             'name' => 'aset_tanah',
    //             'perms' => 'registrasi.aset_tanah',
    //             'title' => 'Aset Tanah',
    //             'url' => '/registrasi/aset-tanah',
    //         ],
    //         [
    //             'name' => 'aset_bangunan',
    //             'perms' => 'registrasi.aset_bangunan',
    //             'title' => 'Aset Bangunan',
    //             'url' => '/registrasi/aset-bangunan',
    //         ],
    //         [
    //             'name' => 'aset_peralatan_mesin',
    //             'perms' => 'registrasi.aset_peralatan_mesin',
    //             'title' => 'Aset Tanah',
    //             'url' => '/registrasi/aset-peralatan-mesin',
    //         ],
    //         [
    //             'name' => 'aset_jalan_irigasi_jaringan',
    //             'perms' => 'registrasi.aset_jalan_irigasi_jaringan',
    //             'title' => 'Aset Jalan Irgasi Jaringan',
    //             'url' => '/registrasi/aset-jalan-irigasi-jaringan',
    //         ],  
    //         [
    //             'name' => 'aset_tetap_lainya',
    //             'perms' => 'registrasi.aset_tetap_lainya',
    //             'title' => 'Aset Tetap Lainya',
    //             'url' => '/registrasi/aset-tetap-lainya',
    //         ],    
    //         [
    //             'name' => 'aset_kontruksi_pembangunan',
    //             'perms' => 'registrasi.aset_kontruksi_pembangunan',
    //             'title' => 'Aset Kontruksi Pembangunan',
    //             'url' => '/registrasi/aset-kontruksi-pembangunan',
    //         ],             
    //     ]
    // ],
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
                'title' => 'Chart of Accounts',
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
                // 'submenu' => [
                //     [
                //         'name' => 'master_dana',
                //         'title' => 'Master Sumber Dana',
                //         'url' => '/master/dana'
                //     ],
                // ],
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
