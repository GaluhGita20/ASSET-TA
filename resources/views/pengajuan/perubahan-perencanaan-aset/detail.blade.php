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
                    {{-- @include('globals.notes') --}}
                    @csrf
                    {{-- data --}}
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Nomor Surat') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="perencanaan_id" class="form-control base-plugin--select2-ajax perencanaan_id"
                                        data-url="{{ rut('ajax.selectCodePerencanaan') }}"
                                        data-url-origin="{{ rut('ajax.selectCodePerencanaan') }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="">{{ __('Pilih Nomor Surat Perencanaan') }}</option>
                                    @if (!empty($record->detailUsulan->perencanaan_id))
                                        <option value="{{ $record->detailUsulan->perencanaan_id }}" selected>{{ $record->detailUsulan->perencanaan->code}}</option>
                                    @endif
                                    </select>   
                                </div>
                                <input type="hidden" name = 'perencanaan_id' value="{{ $record->detailUsulan->perencanaan_id }}">
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class="col-form-label">{{ __('Nama Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <select name="usulan_id" class="form-control base-plugin--select2-ajax usulan_id"
                                        data-url="{{ rut('ajax.selectUsulanDetail') }}"
                                        data-url-origin="{{ rut('ajax.selectUsulanDetail') }}"
                                        placeholder="{{ __('Pilih Salah Satu') }}" disabled>
                                        <option value="">{{ __('Pilih Detail Aset') }}</option>
                                    @if (!empty($record->detailUsulan->asetd->name))
                                        <option value="{{ $record->detailUsulan->asetd->id }}" selected>{{ $record->detailUsulan->asetd->name}}</option>
                                    @endif  
                                    </select> 
                                </div>
                                <input type="hidden" name="usulan_id" value ="{{ $record->usulan_id }}">
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Spesifikasi Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="form-control" name="spesifikasi" id="spesifikasi" value ="{{$record->detailUsulan->desc_spesification}}" disabled>{{$record->detailUsulan->desc_spesification}}</textarea>
                                    <span style="font-size: 11px">{{ __('*Contoh Bahan: Kaca
                                        Ukuran: 100 Ml
                                        Panjang: 20 cm
                                        Lebar : 20 cm
                                        Frekuensi: 100Hz') }}</span>
                                </div>
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Jumlah Disetujui') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input name="jumlah_disetujui" id="jumlah_disetujui" class="form-control base-plugin--datepicker"
                                        placeholder="{{ __('Tanggal Pengajuan Surat') }}"  value ="{{$record->detailUsulan->qty_agree}}" disabled>
                                </div>
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Pagu Unit Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <div class="input-group">
                                        <input type="text" name="pagu_unit" id="pagu_unit" class="form-control base-plugin--inputmask_currency text-right" value="{{ number_format($record->detailUsulan->HPS_unit_cost, 0, ',', ',') }}" disabled>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Pagu Total Aset') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <div class="input-group">
                                        <input type="text" name="pagu_total" id="pagu_totak" class="form-control base-plugin--inputmask_currency text-right" value="{{ number_format($record->detailUsulan->HPS_total_cost, 0, ',', ',') }}" disabled>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                rupiah
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Tanggal Perubahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <input name="date" class="form-control base-plugin--datepicker"
                                        placeholder="{{ __('Tanggal Pengajuan Surat') }}"  value="{{ $record->update_date }}" data-date-end-date="{{ now() }}" disabled>
                                </div>
                            </div>
                
                            <div class="form-group row">
                                <div class="col-2 pr-0">
                                    <label class="col-form-label">{{ __('Catatan Perubahan') }}</label>
                                </div>
                                <div class="col-10 parent-group">
                                    <textarea class="base-plugin--summernote"  name="note" placeholder="{{ __('Catatan Alasan Penolakan') }}" value="{{$record->note}}" disabled>{{$record->note}}</textarea>
                                </div>
                                <input type="hidden" name="note" value="{{$record->note}}">
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end of header -->

    <!-- card 3 -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card card-custom">
                {{-- <div class="card-header">
                    <h3 class="card-title">Surat Pengajuan Perubahan Usulan</h3>
                </div> --}}
                
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

    @if (request()->route()->getName() == $routes.'.detail' && collect(auth()->user()->roles)->contains('name', 'PPK'))
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

                                                        @if($module =='perubahan-perencanaan')
                                                            @if($flow->role->name == 'Umum' && $flow->order == 1)
                                                                title="{{ $flow->show_type }}">Departemen Penunjang
                                                            @else 
                                                                title="{{ $flow->show_type }}">{{ $flow->role->name }}
                                                            @endif
                                                        @elseif($module =='perubahan-usulan-umum' )
                                                            @if($flow->role->name == 'Umum' && $flow->order == 1)
                                                                title="{{ $flow->show_type }}">Departemen Unit
                                                            @elseif($flow->role->name == 'Umum' && $flow->order == 2)
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
