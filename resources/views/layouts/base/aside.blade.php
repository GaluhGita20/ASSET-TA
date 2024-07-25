<div id="kt_aside" class="aside aside-left aside-fixed d-flex flex-column flex-row-auto page-with-light-sidebar">
    {{-- Brand --}}
    <div class="brand flex-column-auto pr-3" id="kt_brand">
        <div class="brand-logo m-auto">
            <a href="{{ yurl('/') }}">
                <img src="{{ '/'.(config('base.logo.aside')) }}" alt="Image"
                    style="max-width: 170px; max-height: 40px;" />
            </a>
        </div>
        {{-- <h6>tes</h6> --}}

        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            {!! \Base::getSVG('assets/media/svg/icons/Navigation/Angle-double-left.svg', 'svg-icon-xl') !!}
        </button>
    </div>

    @if (auth()->check())
        {{-- Aside menu --}}
        <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
            <div id="kt_aside_menu" class="aside-menu my-4" resize="true" data-menu-vertical="1" data-menu-scroll="1"
                data-menu-dropdown-timeout="250">
                {{-- penunjang
                    ==> 2 tampilan
                non penunjang 1 tampilan
                --}}
                
                @if (config('base.custom-menu'))
                    {{-- Custom menu --}}
                    
                    @php
                    
                        $roles = collect(auth()->user()->roles);
                       // dd($roles,auth()->user()->position->location->name , auth()->user()->position->location->parent_id )
                    @endphp
                    <div class="custom-menu">
                        <ul class="menu-nav nav">
                            @foreach (config('backendmenu') as $menu)
                                {{-- menu perms tidak kosong dan user tidak memiliki perms --}}
                                @if (!empty($menu['perms']) && !auth()->user()->checkPerms($menu['perms'] . '.view'))
                                    @continue
                                @endif

                                @php
                                    $umum =
                                    [
                                        'name' => 'perencanaan',
                                        'title' => 'Pengajuan Perencanaan',
                                        'icon' => 'fa fa-road',
                                        'submenu' => [
                                            [
                                                'name' => 'perencanaan-aset-pelayanan',
                                                'perms' => 'perencanaan-aset-pelayanan',
                                                // 'icon' => 'fa fa-road',
                                                'title' => 'Unit Umum',
                                                'url' => '/pengajuan/perencanaan-aset-pelayanan',
                                            ],
                                        ],
                                    ];

                                    $penunjang = 
                                    [
                                        'name' => 'perencanaan',
                                        'title' => 'Pengajuan Perencanaan',
                                        'icon' => 'fa fa-road',
                                        'submenu' => [
                                            [
                                                'name' => 'perencanaan-aset',
                                                'perms' => 'perencanaan-aset',
                                                // 'icon' => 'fa fa-road',
                                                'title' => 'Unit Penunjang',
                                                'url' => '/pengajuan/perencanaan-aset',
                                            ],
                                        ],
                                    ];

                                    $umums =
                                    
                                        [
                                            'name' => 'perubahan',
                                            'title' => 'Perubahan Perencanaan',
                                            'icon' => 'fa fa-file-alt',
                                            'submenu' =>[
                                                [
                                                    'name' => 'perubahan-usulan-umum',
                                                    'perms' => 'perubahan-usulan-umum',
                                                    'title' => 'Unit Umum',
                                                    'url' => '/pengajuan/perubahan-usulan-umum',
                                                ],
                                            ],
                                        ];
                                    

                                    $penunjangs = 
                                    
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
                                            ],
                                        ];
                                    

                                @endphp

                                @if($menu['name'] =='perencanaan' && $roles->contains('name', 'Umum'))
                                    @foreach ($menu['submenu'] as $value) 
                                        @if(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perencanaan-aset-pelayanan' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id !=3) 
                                            {!! \Base::renderMenuTree($umum) !!}
                                        @elseif(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perencanaan-aset-pelayanan' && auth()->user()->position->location->parent_id ==3 && auth()->user()->checkPerms($value['perms'] . '.view'))
                                            @continue;
                                        @elseif(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perencanaan-aset' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id !=3)
                                            @continue;
                                        @elseif($value['name'] =='perencanaan-aset' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id ==3)
                                            {!! \Base::renderMenuTree($penunjang) !!}
                                        @endif
                                    @endforeach

                                    @if($roles->contains('name', 'Umum') && auth()->user()->position->location->name == "Bidang Penunjang Medik dan Non Medik")
                                        {{-- {!! \Base::renderMenuTree($menu) !!} --}}
                                        {!! \Base::renderMenuTree($menu) !!}
                                    @endif
                                @elseif($menu['name'] == 'perubahan' && $roles->contains('name', 'Umum'))
                                    @foreach ($menu['submenu'] as $value) 
                                        @if(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perubahan-usulan-umum' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id !=3) 
                                            {!! \Base::renderMenuTree($umums) !!}
                                        @elseif(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perubahan-usulan-umum' && auth()->user()->position->location->parent_id ==3 && auth()->user()->checkPerms($value['perms'] . '.view'))
                                            @continue;
                                        @elseif(auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $value['name'] =='perubahan-perencanaan' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id !=3)
                                            @continue;
                                        @elseif($value['name'] =='perubahan-perencanaan' && auth()->user()->checkPerms($value['perms'] . '.view') && auth()->user()->position->location->parent_id ==3)
                                            {!! \Base::renderMenuTree($penunjangs) !!}
                                        @endif
                                    @endforeach

                                    @if($roles->contains('name', 'Umum') && auth()->user()->position->location->name == "Bidang Penunjang Medik dan Non Medik")
                                        {{-- {!! \Base::renderMenuTree($menu) !!} --}}
                                        {!! \Base::renderMenuTree($menu) !!}
                                    @endif
                                @else
                                    {!! \Base::renderMenuTree($menu) !!}
                                @endif

                            @endforeach
                        </ul>
                    </div>
                @else
                    {{-- Default metronic menu --}}
                    <ul class="menu-nav">
                        @foreach (config('backendmenu') as $menu)
                            @if (isset($menu['permission']) && !auth()->user()->checkPerms($menu['permission']))
                                @continue;
                            @endif

                            @if($roles->contains('Umum') &&  auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $menu['name'] =='perencanaan-aset-pelayanan')
                                {!! \Base::renderMenuTree($menu) !!}
                            @elseif($roles->contains('Umum') && auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $menu['name'] =='perencanaan-aset-pelayanan' && auth()->user()->position->location->parent_id ==3)
                                @continue;
                            @elseif($roles->contains('Umum') && auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $menu['name'] =='perencanaan-aset')
                                @continue;
                            @elseif($roles->contains('Umum') && auth()->user()->position->location->name !="Bidang Penunjang Medik dan Non Medik" && $menu['name'] =='perencanaan-aset' && auth()->user()->position->location->parent_id !=3 )
                                @continue;
                            @elseif($roles->contains('Umum') && auth()->user()->position->location->name =="Bidang Penunjang Medik dan Non Medik" && auth()->user()->position->location->level != 'department')
                                {!! \Base::renderMenuTree($menu) !!}
                            @elseif(!$roles->contains('Umum'))
                                {!! \Base::renderMenuTree($menu) !!}
                            @endif
                            {{-- {!! \Base::renderAsideMenu($menu) !!} --}}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
