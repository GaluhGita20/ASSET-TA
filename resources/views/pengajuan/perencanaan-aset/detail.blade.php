@extends('layouts.pageSubmit')

@section('action', route($routes . '.update', $record->id))

@section('card-body')
@section('page-content')
    @method('PATCH')
    @csrf
    <!-- header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">@yield('card-title', $title)</h3>
                    <div class="card-toolbar">
                        @include('layouts.forms.btnBackTop')
                    </div>
                </div>

                <div class="card-body">
                    @include('globals.notes')
                    
                    @csrf
                    @include('pengajuan.perencanaan-aset.includes.header') 
                    {{-- data --}}
                    <div class="row">
                        <div class="col-md-12">
                            {{-- <div class="form-group row">
                                <label class="col-md-2 col-form-label">{{ __('Diteruskan Kepada Yth') }}</label>
                                <div class="col-md-10 parent-group">
                                    <select name="cc[]" class="form-control base-plugin--select2-ajax"
                                        data-url="{{ route('ajax.selectUser', ['search' => 'level_department']) }}" multiple
                                        placeholder="{{ __('Pilih Beberapa') }}">
                                        <option value="">{{ __('Pilih Beberapa') }}</option>
                                        @foreach ($record->cc as $user)
                                            <option value="{{ $user->id }}" selected>
                                                {{ $user->name . ' (' . $user->position->name ?? '' . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Keterangan Tambahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea name="note" class="base-plugin--summernote" placeholder="{{ __('Keterangan Tambahan') }}" data-height="200">{!! $record->note  !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 2 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Lampiran Daftar Kebutuhan</h3>
                </div>
                <div class="card-body p-8">
                    @include('pengajuan.perencanaan-aset.detail.index')
                </div>
            </div>
        </div>
    </div>

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Surat Pengajuan Pembelian</h3>
                </div>
                <div class="card-body p-8">
                    @include('pengajuan.perencanaan-aset.includes.letter')
                </div>
                @if (request()->route()->getName() == $routes.'.approval')
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        @if ($record->checkAction('approval', $perms))
                            @include('layouts.forms.btnBack')
                            @include('layouts.forms.btnDropdownApproval')
                            @include('layouts.forms.modalReject')
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $colors = [
            1 => 'primary',
            2 => 'info',
        ];
    @endphp

    @if (request()->route()->getName() == $routes.'.detail')
        <div class="row">
            <div class="col-md-6" style="margin-top:20px!important;">
                <div class="card card-custom" style="height:100%;">
                    <div class="card-header">
                        <h4 class="card-title">Alur Persetujuan</h4>
                    </div>
                    <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;display:grid;">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="d-flex flex-column mr-5">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @php
                                            $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                        @endphp
                                        @if ($menu->flows()->get()->groupBy('order')->count() == 0)
                                            <span class="label label-light-info font-weight-bold label-inline mt-3"
                                                data-toggle="tooltip">Data tidak tersedia.</span>
                                        @else
                                            @foreach ($orders = $menu->flows()->get()->groupBy('order') as $i => $flows)
                                                @foreach ($flows as $j => $flow)
                                                    <span class="label label-light-{{ $colors[$flow->type] }} font-weight-bold label-inline"
                                                        data-toggle="tooltip"

                                                        @if($flow->menu_id == 15)
                                                            @if($flow->role->name == 'Umum' && $flow->position->id == 4)
                                                                title="{{ $flow->show_type }}">Departemen Penunjang
                                                            @elseif($flow->role->name == 'Umum' && $flow->position->id  == 3)
                                                                title="{{ $flow->show_type }}">Departemen Unit
                                                            @else 
                                                                title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                            @endif
                                                        @elseif($flow->menu_id == 1)
                                                            @if($flow->role->name == 'Umum')
                                                                title="{{ $flow->show_type }}">Departemen Penunjang
                                                            @else 
                                                                title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                            @endif
                                                        @else
                                                            @if($flow->role->name == 'Umum')
                                                                title="{{ $flow->show_type }}">Departemen Unit
                                                            @else 
                                                                title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                            @endif
                                                        @endif
                                                    </span>
                                                
                                                    {{-- <span
                                                        class="label label-light-{{ $colors[$flow->type] }} font-weight-bold label-inline"
                                                        data-toggle="tooltip"  {{  $flow->role->name == 'Umum'}}  ?? title="{{ $flow->show_type }}">Departemen
                                                        @else 
                                                            title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                        @endif
                                                    </span> --}}

                                                    @if (!($i === $orders->keys()->last() && $j === $flows->keys()->last()))
                                                        <i class="fas fa-angle-double-right text-muted mx-2"></i>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="margin-top:20px!important;">
                <div class="card card-custom" style="height:100%;">
                    <div class="card-header">
                        <h4 class="card-title">Informasi</h4>
                    </div>

                    <div class="card-body" style="padding: 10px 1.75rem 10px 1.75rem;">
                        <div class="d-flex align-items-center justify-content-between flex-lg-wrap flex-xl-nowrap p-4">
                            <div class="d-flex flex-column mr-5">
                                <p class="text-dark-50">
                                    Sebelum submit pastikan data {!! $title !!} tersebut sudah sesuai.
                                </p>
                            </div>

                            <div class="ml-lg-0 ml-xxl-6 ml-6 flex-shrink-0">
                                @php
                                    $menu = \App\Models\Globals\Menu::where('module', $module)->first();
                                    $count = $menu->flows()->count();
                                    $submit = $count == 0 ? 'disabled' : 'enabled';
                                @endphp
                                <div style="display: none">
                                    @include('layouts.forms.btnBack')
                                </div>
                                @include('layouts.forms.btnDropdownSubmit')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@show
@endsection
